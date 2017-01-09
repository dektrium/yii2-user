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

use dektrium\user\service\ConfirmationService;
use dektrium\user\traits\ServiceTrait;
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
    use ServiceTrait;

    /**
     * @var string User's email or username
     */
    public $login;

    /**
     * @var string User's plain password
     */
    public $password;

    /**
     * @var string Whether to remember the user
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
            'login' => Yii::t('user', 'Login'),
            'password' => Yii::t('user', 'Password'),
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
            'confirmationValidate' => ['login', 'validateConfirmationStatus'],
            'blockValidate' => ['login', 'validateBlockStatus'],
            'rememberMe' => ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates user's password.
     *
     * @param string $attribute
     */
    public function validatePassword($attribute)
    {
        if ($this->user === null || !$this->user->validatePassword($this->password)) {
            $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
        }
    }

    /**
     * Validates user's confirmation status.
     *
     * @param string $attribute
     */
    public function validateConfirmationStatus($attribute)
    {
        $service = $this->getConfirmationService();

        if ($this->user !== null && $this->user->getIsAdmin()) {
            return;
        }

        if ($this->user !== null && $service->isEnabled && !$service->isLoginWhileUnconfirmedEnabled) {
            if ($service->isEmailConfirmationEnabled && !$this->user->getIsConfirmed()) {
                $this->addError($attribute, Yii::t('user', 'You need to confirm your email address'));
            }
            if ($service->isAdminApprovalEnabled && !$this->user->isApproved()) {
                $this->addError($attribute, Yii::t('user', 'Your account needs to be approved by administrator'));
            }
        }
    }

    /**
     * Validates user's block status.
     *
     * @param string $attribute
     */
    public function validateBlockStatus($attribute)
    {
        if ($this->user !== null && $this->user->getIsBlocked()) {
            $this->addError($attribute, Yii::t('user', 'Your account has been blocked'));
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
            /** @var User $user */
            $user = \Yii::createObject(User::className());
            $this->user = $user::find()->byEmailOrUsername($this->login)->one();

            return true;
        } else {
            return false;
        }
    }
}
