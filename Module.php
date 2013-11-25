<?php namespace dektrium\user;

use \yii\base\Module as BaseModule;

/**
 * This is the main module class for the Dektrium user module.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Module extends BaseModule
{
    const VERSION = '0.1.0-DEV';

    /**
     * @inheritdoc
     */
    public $controllerMap = [
        'registration' => '\dektrium\user\controllers\RegistrationController',
        'auth' => '\dektrium\user\controllers\AuthController',
    ];

    /**
     * @var string Model that will be used in loginAction.
     */
    public $loginForm = '\dektrium\user\models\LoginForm';

    /**
     * @var string Model that will be used in resendAction.
     */
    public $resendForm = '\dektrium\user\models\ResendForm';

    /**
     * @var int The time you want the user will be remembered without asking for credentials.
     * By default rememberFor is two weeks.
     */
    public $rememberFor = 1209600;

    /**
     * @var array Url where the user will be redirected to after registration.
     */
    public $registrationRedirectUrl = ['/user/auth/login'];

    /**
     * @var bool Whether to enable "Trackable" behavior.
     */
    public $trackable = false;

    /**
     * @var bool Whether to enable "Confirmable" behavior.
     */
    public $confirmable = true;

    /**
     * @var bool Whether to allow login without confirmation.
     */
    public $allowUnconfirmedLogin = false;

    /**
     * @var int The time before a sent confirmation token becomes invalid.
     * By default confirmWithin is 24 hours.
     */
    public $confirmWithin = 86400;

    /**
     * @var string View that will be rendered by Mailer.compose().
     */
    public $confirmationMessageView = '@user/views/mail/confirmation.php';

    /**
     * @var string Subject of confirmation message.
     */
    public $confirmationMessageSubject = 'Account confirmation on Site.com';

    /**
     * @var string|array Message sender.
     */
    public $messageSender = 'no-reply@example.com';

    /**
     * @var int Cost parameter used by the Blowfish hash algorithm.
     */
    public $cost = 10;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->getI18n()->translations['user*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => __DIR__.'/messages',
            'fileMap' => [
                'user' => 'user.php'
            ]
        ];
        $this->setAliases([
            '@user' => __DIR__
        ]);
    }
}
