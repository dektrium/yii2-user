<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\forms;

use yii\base\Model;
use dektrium\user\helpers\Password;

/**
 * LoginForm is the model behind the login form.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Login extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var bool Whether to remember the user.
     */
    public $rememberMe = false;

    /**
     * @var \dektrium\user\models\User
     */
    protected $user;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => \Yii::t('user', 'Email'),
            'password'   => \Yii::t('user', 'Password'),
            'rememberMe' => \Yii::t('user', 'Remember me next time'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['password', function ($attribute) {
                if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
                    $this->addError($attribute, \Yii::t('user', 'Invalid email or password'));
                }
            }],
            ['email', function ($attribute) {
                if ($this->user !== null) {
                    $confirmationRequired = $this->module->confirmable && !$this->module->allowUnconfirmedLogin;
                    if ($confirmationRequired && !$this->user->isConfirmed) {
                        $this->addError($attribute, \Yii::t('user', 'You need to confirm your email address'));
                    }
                    if ($this->user->getIsBlocked()) {
                        $this->addError($attribute, \Yii::t('user', 'Your account has been blocked'));
                    }
                }
            }],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return \Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->getModule()->rememberFor : 0);
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'login-form';
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $query = $this->getModule()->factory->createUserQuery();
            $this->user = $query->where(['email' => $this->email])->one();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return null|\dektrium\user\Module
     */
    protected function getModule()
    {
        return \Yii::$app->getModule('user');
    }
}
