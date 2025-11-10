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

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\helpers\Password;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\traits\ModuleTrait; // Required by ModuleTrait and for static calls
// Added for type hinting
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * LoginForm get user's login and password, validates them and logs the user in. If user has been blocked, it adds
 * an error to login form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginForm extends Model
{
    use ModuleTrait;

    /** @var string|null User's email or username */
    public $login = null;

    /** @var string|null User's plain password. Nullable if module debug is enabled. */
    public $password = null;

    /** @var bool Whether to remember the user */
    public $rememberMe = false;

    /** @var User|null The user found based on login field */
    protected $user = null;

    /** @var Finder */
    protected $finder;

    /**
     * LoginForm constructor.
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct($finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /**
     * Gets all users to generate the dropdown list when in debug mode.
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function loginList()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');

        if (!$module->debug) {
            return []; // Only available in debug mode
        }

        /** @var User $userModel */
        $userModelClass = $module->modelMap['User'];
        $users = $userModelClass::find()->where(['blocked_at' => null])->all();

        return ArrayHelper::map($users, 'username', function ($user) {
            return $user->username . ' (' . $user->email . ')';
        });
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'login' => Yii::t('user', 'Login'),
            'password' => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember me next time'),
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        /** @var Module $module */
        $module = $this->module; // Use trait getter

        $rules = [
            'loginTrim' => ['login', 'trim'],
            'requiredFields' => [['login'], 'required'],
            'loginValidation' => [
                'login',
                function ($attribute) use ($module) {
                    if ($this->user === null) {
                        $this->addError($attribute, Yii::t('user', 'Invalid login or password')); // Generic error if user not found

                        return;
                    }
                    $confirmationRequired = $module->enableConfirmation && !$module->enableUnconfirmedLogin;

                    if ($confirmationRequired && !$this->user->getIsConfirmed()) {
                        $this->addError($attribute, Yii::t('user', 'You need to confirm your email address'));
                    }

                    if ($this->user->getIsBlocked()) {
                        $this->addError($attribute, Yii::t('user', 'Your account has been blocked'));
                    }
                }
            ],
            'rememberMe' => ['rememberMe', 'boolean'],
        ];

        // Add password validation rules only if not in debug mode
        // or if password is required (original logic was slightly different)
        if (!$module->debug) {
            $rules['passwordRequired'] = ['password', 'required'];
            $rules['passwordValidate'] = [
                'password',
                function ($attribute) {
                    if ($this->user === null || $this->password === null || !Password::validate($this->password, $this->user->password_hash)) {
                        $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                    }
                }
            ];
        }

        return $rules;
    }

    /**
     * Validates form and logs the user in.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) { // Ensure user property is populated by beforeValidate()
            if ($this->user instanceof User) {
                $isLogged = Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0);

                if ($isLogged) {
                    $this->user->updateAttributes(['last_login_at' => time()]);
                }

                return $isLogged;
            } else {
                // This case should technically not be reached if validation passes,
                // but added for safety.
                Yii::warning('Login validation passed but user object is not available.', __METHOD__);

                return false;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function formName()
    {
        return 'login-form';
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->user = $this->finder->findUserByUsernameOrEmail(trim((string)$this->login));

            return true;
        }

        return false;
    }
}
