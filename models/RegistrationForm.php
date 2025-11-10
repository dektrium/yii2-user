<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\models;

use AlexeiKaDev\Yii2User\services\RegistrationService;
use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use Yii;
use yii\base\Model;

/**
 * Registration form collects user input on registration process, validates it and creates new User model.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationForm extends Model
{
    use ModuleTrait;

    /**
     * @var string|null User email address
     */
    public $email = null;

    /**
     * @var string|null Username
     */
    public $username = null;

    /**
     * @var string|null Password
     */
    public $password = null;

    /** @var RegistrationService The registration service instance. */
    private $registrationService;

    /**
     * RegistrationForm constructor.
     * @param RegistrationService $registrationService
     * @param array $config
     */
    public function __construct(RegistrationService $registrationService, array $config = [])
    {
        $this->registrationService = $registrationService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $user = $this->module->modelMap['User'];

        return [
            // username rules
            'usernameTrim' => ['username', 'trim'],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernamePattern' => ['username', 'match', 'pattern' => $user::$usernameRegexp],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique' => [
                'username',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('user', 'This username has already been taken')
            ],
            // email rules
            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailUnique' => [
                'email',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('user', 'This email address has already been taken')
            ],
            // password rules
            'passwordRequired' => ['password', 'required', 'skipOnEmpty' => $this->module->enableGeneratingPassword],
            'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'Email'),
            'username' => Yii::t('user', 'Username'),
            'password' => Yii::t('user', 'Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }

    /**
     * Registers a new user account using RegistrationService.
     * If registration was successful it will set flash message.
     *
     * @return bool True if registration was successful
     */
    public function register(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->registrationService->register($this);

        if ($user instanceof User) {
            Yii::$app->session->setFlash(
                'info',
                Yii::t(
                    'user',
                    'Your account has been created and a message with further instructions has been sent to your email'
                )
            );

            return true;
        } else {
            return false;
        }
    }
}
