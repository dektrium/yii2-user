<?php


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
use AlexeiKaDev\Yii2User\models\RegistrationForm;
use AlexeiKaDev\Yii2User\models\ResendForm;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\services\UserConfirmationService;
use AlexeiKaDev\Yii2User\traits\AjaxValidationTrait;
use AlexeiKaDev\Yii2User\traits\EventTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered after creating RegistrationForm class.
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_BEFORE_REGISTER = 'beforeRegister';

    /**
     * Event is triggered after successful registration.
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_AFTER_REGISTER = 'afterRegister';

    /**
     * Event is triggered before connecting user to social account.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_CONNECT = 'beforeConnect';

    /**
     * Event is triggered after connecting user to social account.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_CONNECT = 'afterConnect';

    /**
     * Event is triggered before confirming user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

    /**
     * Event is triggered before confirming user.
     * Triggered with \AlexeiKaDev\Yii2User\events\UserEvent.
     */
    public const EVENT_AFTER_CONFIRM = 'afterConfirm';

    /**
     * Event is triggered after creating ResendForm class.
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_BEFORE_RESEND = 'beforeResend';

    /**
     * Event is triggered after successful resending of confirmation email.
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_AFTER_RESEND = 'afterResend';

    protected $finder;
    protected $userConfirmationService;

    public function __construct(
        string $id,
        Module $module,
        Finder $finder,
        UserConfirmationService $userConfirmationService,
        array $config = []
    ) {
        $this->finder = $finder;
        $this->userConfirmationService = $userConfirmationService;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['register', 'connect'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm', 'resend'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays the registration page.
     * After successful registration if enableConfirmation is enabled shows info message otherwise
     * redirects to home page.
     *
     * @return string|Response
     * @throws \yii\web\HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRegister()
    {
        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException();
        }

        /** @var RegistrationForm $model */
        $model = Yii::createObject(RegistrationForm::class);
        $event = $this->getFormEvent($model);

        $this->trigger(self::EVENT_BEFORE_REGISTER, $event);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            $this->trigger(self::EVENT_AFTER_REGISTER, $event);

            return $this->render('/message', [
                'title' => Yii::t('user', 'Your account has been created'),
                'module' => $this->module,
            ]);
        }

        return $this->render('register', [
            'model' => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Displays page where user can create new account that will be connected to social account.
     *
     * @param string $code
     *
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionConnect($code)
    {
        $account = $this->finder->findAccount()->byCode($code)->one();

        if ($account === null || $account->getIsConnected()) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = Yii::createObject([
            'class' => User::class,
            'scenario' => 'connect',
            'username' => $account->username,
            'email' => $account->email,
        ]);

        $event = $this->getConnectEvent($account, $user);

        $this->trigger(self::EVENT_BEFORE_CONNECT, $event);

        if ($user->load(Yii::$app->request->post()) && $user->create()) {
            $account->connect($user);
            $this->trigger(self::EVENT_AFTER_CONNECT, $event);
            Yii::$app->user->login($user, $this->module->rememberFor);

            return $this->goBack();
        }

        return $this->render('connect', [
            'model' => $user,
            'account' => $account,
        ]);
    }

    /**
     * Confirms user's account. If confirmation was successful logs the user and shows success message. Otherwise
     * shows error message.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null || !$this->module->enableConfirmation) {
            throw new NotFoundHttpException();
        }

        if ($this->userConfirmationService->attempt($user, $code)) {
            if (Yii::$app->getUser()->getIsGuest()) {
                Yii::$app->user->login($user, $this->module->rememberFor);
            }
        }

        return $this->render('/message', [
            'title' => Yii::t('user', 'Account confirmation'),
            'module' => $this->module,
        ]);
    }

    /**
     * Displays page where user can request new confirmation token. If resending was successful, displays message.
     *
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionResend()
    {
        if (!$this->module->enableConfirmation) {
            throw new NotFoundHttpException();
        }

        /** @var ResendForm $model */
        $model = Yii::createObject(ResendForm::class);
        $event = $this->getFormEvent($model);

        $this->trigger(self::EVENT_BEFORE_RESEND, $event);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->resend()) {
            $this->trigger(self::EVENT_AFTER_RESEND, $event);

            return $this->render('/message', [
                'title' => Yii::t('user', 'A new confirmation link has been sent'),
                'module' => $this->module,
            ]);
        }

        return $this->render('resend', [
            'model' => $model,
        ]);
    }
}
