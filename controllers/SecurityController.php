<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\controllers;

use dektrium\user\Finder;
use dektrium\user\models\Account;
use dektrium\user\models\LoginForm;
use dektrium\user\models\TwoFactorForm;
use dektrium\user\models\User;
use dektrium\user\Module;
use dektrium\user\traits\AjaxValidationTrait;
use dektrium\user\traits\EventTrait;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Controller that manages user authentication process.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SecurityController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before logging user in.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_BEFORE_LOGIN = 'beforeLogin';

    /**
     * Event is triggered after logging user in.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_AFTER_LOGIN = 'afterLogin';

    /**
     * Event is triggered before logging user out.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_LOGOUT = 'beforeLogout';

    /**
     * Event is triggered after logging user out.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_LOGOUT = 'afterLogout';

    /**
     * Event is triggered before authenticating user via social network.
     * Triggered with \dektrium\user\events\AuthEvent.
     */
    const EVENT_BEFORE_AUTHENTICATE = 'beforeAuthenticate';

    /**
     * Event is triggered after authenticating user via social network.
     * Triggered with \dektrium\user\events\AuthEvent.
     */
    const EVENT_AFTER_AUTHENTICATE = 'afterAuthenticate';

    /**
     * Event is triggered before connecting social network account to user.
     * Triggered with \dektrium\user\events\AuthEvent.
     */
    const EVENT_BEFORE_CONNECT = 'beforeConnect';

    /**
     * Event is triggered before connecting social network account to user.
     * Triggered with \dektrium\user\events\AuthEvent.
     */
    const EVENT_AFTER_CONNECT = 'afterConnect';

    /**
     * Event is triggered before confirm two factor authentication code.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_BEFORE_TFA = 'beforeTFA';

    /**
     * Event is triggered after confirm two factor authentication code.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_AFTER_TFA = 'afterTFA';

    /**
     * @var string
     */
    public $tfaCounterKey = 'tfa-count';

    /**
     * Count attempts typing TFA code
     *
     * @var int
     */
    public $tfaCount = 10;

    /**
     * @var string
     */
    public $tfaCredentialsKey = 'credentials';

    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['login', 'auth', 'two-factor-authentication'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['login', 'auth', 'logout'], 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /** @inheritdoc */
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::className(),
                // if user is not logged in, will try to log him in, otherwise
                // will try to connect social account to user.
                'successCallback' => \Yii::$app->user->isGuest
                    ? [$this, 'authenticate']
                    : [$this, 'connect'],
            ],
        ];
    }

    /**
     * Displays the login page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        /** @var LoginForm $model */
        $model = \Yii::createObject(LoginForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(\Yii::$app->getRequest()->post())) {
            if (
                $this->module->enableTwoFactorAuthentication
                && $model->hasTFA()
            ) {
                if ($model->validate()) {
                    $attributes = $model->scenarios()[$model::SCENARIO_TFA_LOGIN];
                    \Yii::$app->session->set($this->tfaCredentialsKey, $model->getAttributes($attributes));
                    \Yii::$app->session->set($this->tfaCounterKey, $this->tfaCount);
                    return $this->redirect(['/user/security/two-factor-authentication']);
                }
            } elseif ($model->login()) {
                $this->trigger(self::EVENT_AFTER_LOGIN, $event);
                return $this->goBack();
            }
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $event = $this->getUserEvent(\Yii::$app->user->identity);

        $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);

        \Yii::$app->getUser()->logout();

        $this->trigger(self::EVENT_AFTER_LOGOUT, $event);

        return $this->goHome();
    }

    /**
     * Display confirm TFA page
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws ExitException
     * @throws InvalidConfigException
     * @throws StaleObjectException
     */
    public function actionTwoFactorAuthentication()
    {
        $credentials = \Yii::$app->session->get($this->tfaCredentialsKey);
        if (false === $this->module->enableTwoFactorAuthentication
            || empty($credentials)) {
            throw new NotFoundHttpException();
        }

        /** @var LoginForm $model */
        $loginForm = \Yii::createObject(LoginForm::className());
        $loginForm->setAttributes($credentials);
        $loginForm->scenario = $loginForm::SCENARIO_TFA_LOGIN;
        $eventLogin = $this->getFormEvent($loginForm);

        if(false === $loginForm->validate()){
            throw new NotFoundHttpException();
        }

        /** @var TwoFactorForm $model */
        $model = \Yii::createObject(TwoFactorForm::className());
        $model->setUserByLogin($loginForm->login);
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model, [$this, 'checkTfaCounter']);

        $this->trigger(self::EVENT_BEFORE_TFA, $event);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate()) {
            $this->checkTfaCounter();
            if($loginForm->login()){
                if ($model->deleteUserRecoveryCode()) {
                    \Yii::$app->session->setFlash(
                        'warning',
                        \Yii::t(
                            'user',
                            'You have {0} recovery keys left',
                            count($model->getRecoveryCodes()) - 1
                        )
                    );
                }

                $this->trigger(self::EVENT_AFTER_TFA, $event);
                $this->trigger(self::EVENT_AFTER_LOGIN, $eventLogin);
                return $this->goBack();
            }
        }

        return $this->render('two-factor', [
            'model' => $model,
        ]);
    }

    /**
     * Tries to authenticate user via social network. If user has already used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *
     * @param ClientInterface $client
     */
    public function authenticate(ClientInterface $client)
    {
        $account = $this->finder->findAccount()->byClient($client)->one();

        if (!$this->module->enableRegistration && ($account === null || $account->user === null)) {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Registration on this website is disabled'));
            $this->action->successUrl = Url::to(['/user/security/login']);
            return;
        }

        if ($account === null) {
            /** @var Account $account */
            $accountObj = \Yii::createObject(Account::className());
            $account = $accountObj::create($client);
        }

        $event = $this->getAuthEvent($account, $client);

        $this->trigger(self::EVENT_BEFORE_AUTHENTICATE, $event);

        if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Your account has been blocked.'));
                $this->action->successUrl = Url::to(['/user/security/login']);
            } else {
                $account->user->updateAttributes(['last_login_at' => time()]);
                \Yii::$app->user->login($account->user, $this->module->rememberFor);
                $this->action->successUrl = \Yii::$app->getUser()->getReturnUrl();
            }
        } else {
            $this->action->successUrl = $account->getConnectUrl();
        }

        $this->trigger(self::EVENT_AFTER_AUTHENTICATE, $event);
    }

    /**
     * Tries to connect social account to user.
     *
     * @param ClientInterface $client
     */
    public function connect(ClientInterface $client)
    {
        /** @var Account $account */
        $account = \Yii::createObject(Account::className());
        $event   = $this->getAuthEvent($account, $client);

        $this->trigger(self::EVENT_BEFORE_CONNECT, $event);

        $account->connectWithUser($client);

        $this->trigger(self::EVENT_AFTER_CONNECT, $event);

        $this->action->successUrl = Url::to(['/user/settings/networks']);
    }

    /**
     * Decrease two factor authentication counter
     */
    protected function checkTfaCounter()
    {
        $session = \Yii::$app->session;

        $tfaCounter = $session->get($this->tfaCounterKey);

        if ($tfaCounter <= 0 && false) {
            $user = \Yii::$app->user;
            $user->acceptableRedirectTypes[] = 'application/json';
            $user->loginRequired();
        }

        $session->set(
            $this->tfaCounterKey,
            $tfaCounter - 1
        );
    }
}
