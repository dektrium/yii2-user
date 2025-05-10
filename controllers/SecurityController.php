<?php

declare(strict_types=1);

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\controllers;

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\models\Account;
use AlexeiKaDev\Yii2User\models\LoginForm;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\traits\AjaxValidationTrait;
use AlexeiKaDev\Yii2User\traits\EventTrait;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
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
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_BEFORE_LOGIN = 'beforeLogin';

    /**
     * Event is triggered after logging user in.
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_AFTER_LOGIN = 'afterLogin';

    /**
     * Event is triggered before logging user out.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_LOGOUT = 'beforeLogout';

    /**
     * Event is triggered after logging user out.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_LOGOUT = 'afterLogout';

    /**
     * Event is triggered before authenticating user via social network.
     * Triggered with \AlexeiKaDev\Yii2User\events\AuthEvent.
     */
    public const EVENT_BEFORE_AUTHENTICATE = 'beforeAuthenticate';

    /**
     * Event is triggered after authenticating user via social network.
     * Triggered with \AlexeiKaDev\Yii2User\events\AuthEvent.
     */
    public const EVENT_AFTER_AUTHENTICATE = 'afterAuthenticate';

    /**
     * Event is triggered before connecting social network account to user.
     * Triggered with \AlexeiKaDev\Yii2User\events\AuthEvent.
     */
    public const EVENT_BEFORE_CONNECT = 'beforeConnect';

    /**
     * Event is triggered before connecting social network account to user.
     * Triggered with \AlexeiKaDev\Yii2User\events\AuthEvent.
     */
    public const EVENT_AFTER_CONNECT = 'afterConnect';

    /** @var Finder */
    protected Finder $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(string $id, Module $module, Finder $finder, array $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'auth'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login', 'auth', 'logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /** @inheritdoc */
    public function actions(): array
    {
        return [
            'auth' => [
                'class' => AuthAction::class,
                // if user is not logged in, will try to log him in, otherwise
                // will try to connect social account to user.
                'successCallback' => Yii::$app->user->isGuest
                    ? [$this, 'authenticate']
                    : [$this, 'connect'],
            ],
        ];
    }

    /**
     * Displays the login page.
     *
     * @return string|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin(): string|Response
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        /** @var LoginForm $model */
        $model = Yii::createObject(LoginForm::class);
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->trigger(self::EVENT_AFTER_LOGIN, $event);

            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        $identity = Yii::$app->user->identity;
        $event = null;

        if ($identity instanceof User) {
            $event = $this->getUserEvent($identity);
        }

        if ($event) {
            $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);
        }

        Yii::$app->user->logout();

        if ($event) {
            $this->trigger(self::EVENT_AFTER_LOGOUT, $event);
        }

        return $this->goHome();
    }

    /**
     * Tries to authenticate user via social network. If user has already used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *
     * @param BaseClientInterface $client The auth client instance.
     * @throws \yii\base\InvalidConfigException
     * @return void
     */
    public function authenticate(BaseClientInterface $client): void
    {
        $account = $this->finder->findAccount()->byClient($client)->one();

        $registrationDisabled = !$this->module->enableRegistration
            && ($account === null || $account->user === null);

        if ($registrationDisabled) {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'Registration on this website is disabled')
            );
            $this->action->successUrl = Url::to(['/user/security/login']);

            return;
        }

        if ($account === null) {
            /** @var Account $account */
            $account = Account::create($client);
        }

        $event = $this->getAuthEvent($account, $client);

        $this->trigger(self::EVENT_BEFORE_AUTHENTICATE, $event);

        if ($account->user instanceof User) {
            if ($account->user->getIsBlocked()) {
                Yii::$app->session->setFlash(
                    'danger',
                    Yii::t('user', 'Your account has been blocked.')
                );
                $this->action->successUrl = Url::to(['/user/security/login']);
            } else {
                $account->user->updateAttributes(['last_login_at' => time()]);
                Yii::$app->user->login($account->user, $this->module->rememberFor);
                $this->action->successUrl = Yii::$app->user->getReturnUrl();
            }
        } else {
            // Если пользователя нет, getConnectUrl() вернет URL для страницы connect/register
            $this->action->successUrl = $account->getConnectUrl();
        }

        $this->trigger(self::EVENT_AFTER_AUTHENTICATE, $event);
    }

    /**
     * Tries to connect social account to user.
     *
     * @param BaseClientInterface $client The auth client instance.
     * @return void
     */
    public function connect(BaseClientInterface $client): void
    {
        /** @var Account $account */
        // Account::connectWithUser($client) уже содержит логику поиска или создания Account
        // и последующей привязки к текущему пользователю Yii::$app->user->identity.
        // Метод connectWithUser также устанавливает flash-сообщения.
        Account::connectWithUser($client);
        $this->action->successUrl = Url::to(['/user/settings/networks']);
    }
}
