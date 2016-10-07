<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\models;

use dektrium\user\Finder;
use dektrium\user\helpers\Password;
use Yii;
use yii\base\Model;
use dektrium\user\traits\ModuleTrait;

/**
 * LoginForm get user's login and password, validates them and logs the user in. If user has been blocked, it adds
 * an error to login form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginForm extends Model
{
    use ModuleTrait;

    /** @var string User's email or username */
    public $login;

    /** @var string User's plain password */
    public $password;

    /** @var string Whether to remember the user */
    public $rememberMe = false;

    /** @var \dektrium\user\models\User */
    protected $user;

    /** @var Finder */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'login'      => Yii::t('user', 'Login'),
            'password'   => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember me next time'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'requiredFields' => [['login', 'password'], 'required'],
            'loginTrim' => ['login', 'trim'],
            'passwordValidate' => ['password', 'validatePassword'],
            'confirmationValidate' => [
                'login',
                function ($attribute) {
                    if ($this->user !== null) {
                        $confirmationRequired = $this->module->enableConfirmation
                            && !$this->module->enableUnconfirmedLogin;
                        if ($confirmationRequired && !$this->user->getIsConfirmed()) {
                            $this->addError($attribute, Yii::t('user', 'You need to confirm your email address'));
                        }
                        if ($this->user->getIsBlocked()) {
                            $this->addError($attribute, Yii::t('user', 'Your account has been blocked'));
                        }
                    }
                }
            ],
            'rememberMe' => ['rememberMe', 'boolean'],
        ];
    }
    
    /**
     * Validates if the hash of the given password is identical to the saved hash in the database.
     *
     * @return void
     * @access public
     */
    public function validatePassword($attribute, $params)
    {
        $error = '';
        $passwordValidate = false;
        if ($this->user !== null) {
            $passwordValidate = Password::validate($this->password, $this->user->password_hash);
        }
   
        if ($this->module->enableLockLoginAfterFailedLogin) {
            $this->validateLoginLock($attribute, $passwordValidate);
        } else {
            if (!$passwordValidate) {
                $this->addError($attribute, Yii::t('user', 'Invalid login or password.'));
            }
            /*if (!empty($error)) {
                $this->addError($attribute, $error);
            }*/
        }
    }
    
    /**
     * Validates if the login is locked.
     *
     * @return void
     * @access private
     * @author jkmssoft
     */
    private function validateLoginLock($attribute, $passwordValidate)
    {
        LoginAttempt::purgeOld();

        $lockTime = 0;
        $ip = md5(Yii::$app->request->getUserIp());
        $loginAttempt = \dektrium\user\models\LoginAttempt::find()->where(['ip' => $ip])->one();

        // is within lock time?
        if ($loginAttempt !== null) {
            // is last attempt long time ago?
            if (time() - $loginAttempt->last_attempt_at
                > $this->module->secondsAfterLastInvalidLoginToResetCounter) {
                // reset attempts
                $loginAttempt->attempts = 0;
            }

            $lockTime = $loginAttempt->getLoginLockTime(); // calculate lock time
        }

        // log attempt only if invalid login and not within lockTime
        if (!$passwordValidate && $lockTime == 0) {
            if ($loginAttempt === null) {
                // create new logAttempt object
                $loginAttempt = Yii::createObject(\dektrium\user\models\LoginAttempt::className());
                $loginAttempt->ip = $ip;
                $loginAttempt->attempts = 0;
            }

            $loginAttempt->attempts++;
            $loginAttempt->last_attempt_at = time();

            $lockTime = $loginAttempt->getLoginLockTime(); // calculate lock time
            $loginAttempt->save();
        }

        if ($lockTime > 0 || !$passwordValidate) {
            $error = Yii::t('user', 'Invalid login or password.');
        }
        if ($lockTime > 0) {
            $error .= ' '.Yii::t('user', 'Login is locked for {0} seconds.', $lockTime);
        }

        if (!empty($error)) {
            $this->addError($attribute, $error);
        }
        if ($lockTime > 0) {
            $this->addError($attribute, $lockTime);
        }
    }

    /**
     * Validates form and logs the user in.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0);
        } else {
            return false;
        }
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'login-form';
    }

    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->user = $this->finder->findUserByUsernameOrEmail(trim($this->login));

            return true;
        } else {
            return false;
        }
    }
}
