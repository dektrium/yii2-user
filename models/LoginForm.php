<?php namespace dektrium\user\models;

use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\Security;

/**
 * LoginForm is the model behind the login form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginForm extends Model
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
     * @var User
     */
    protected $identity;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['password', 'validatePassword'],
            ['email', 'validateConfirmation'],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates the password.
     */
    public function validatePassword()
    {
        if ($this->identity === null || !Security::validatePassword($this->password, $this->identity->password_hash)) {
            $this->addError('password', 'Invalid login or password');
        }
    }

    /**
     * Validates whether user has confirmed email.
     */
    public function validateConfirmation()
    {
        $module = \Yii::$app->controller->module;
        if ($this->identity !== null
            && $module->confirmable
            && !$module->allowUnconfirmedLogin
            && !$this->identity->isConfirmed
        ) {
            $this->addError('password', 'You must confirm your email before login');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            \Yii::$app->getUser()->login($this->identity, $this->rememberMe ? \Yii::$app->controller->module->rememberFor : 0);

            return true;
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
            $query = new ActiveQuery(['modelClass' => \Yii::$app->getUser()->identityClass]);
            $this->identity = $query->where(['email' => $this->email])->one();

            return true;
        } else {
            return false;
        }
    }
}
