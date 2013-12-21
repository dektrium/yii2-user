<?php namespace dektrium\user\forms;

use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\Security;

/**
 * LoginForm is the model behind the login form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Login extends Model
{
	/**
	 * @var string
	 */
	public $login;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var bool Whether to remember the user.
	 */
	public $rememberMe = false;

	/**
	 * @var string
	 */
	public $verifyCode;

	/**
	 * @var \dektrium\user\models\User
	 */
	protected $identity;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		switch ($this->getModule()->loginType) {
			case 'email':
				$loginLabel = 'Email';
				break;
			case 'username':
				$loginLabel = 'Username';
				break;
			case 'both':
				$loginLabel = 'Email or username';
				break;
			default:
				throw new \RuntimeException;
		}

		return [
			'login' => \Yii::t('user', $loginLabel),
			'password' => \Yii::t('user', 'Password'),
			'rememberMe' => \Yii::t('user', 'Remember me next time'),
			'verifyCode' => \Yii::t('user', 'Verification Code'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['login', 'password'], 'required'],
			['password', 'validatePassword'],
			['login', 'validateConfirmation'],
			['rememberMe', 'boolean'],
			['verifyCode', 'captcha', 'skipOnEmpty' => !in_array('login', $this->getModule()->captcha)]
		];
	}

	/**
	 * Validates the password.
	 */
	public function validatePassword()
	{
		if ($this->identity === null || !Security::validatePassword($this->password, $this->identity->password_hash)) {
			$this->addError('password', \Yii::t('user', 'Invalid login or password'));
		}
	}

	/**
	 * Validates whether user has confirmed his account.
	 */
	public function validateConfirmation()
	{
		if ($this->identity !== null
			&& $this->getModule()->confirmable
			&& !$this->getModule()->allowUnconfirmedLogin
			&& !$this->identity->isConfirmed
		) {
			$this->addError('login', \Yii::t('user', 'You must confirm your account before logging in'));
		}
	}

	/**
	 * Logs in a user using the provided username and password.
	 *
	 * @return boolean whether the user is logged in successfully
	 */
	public function login()
	{
		if ($this->validate()) {
			\Yii::$app->getUser()->login($this->identity, $this->rememberMe ? $this->getModule()->rememberFor : 0);

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
			switch ($this->getModule()->loginType) {
				case 'email':
					$condition = ['email' => $this->login];
					break;
				case 'username':
					$condition = ['username' => $this->login];
					break;
				case 'both':
					$condition = ['or', ['email' => $this->login], ['username' => $this->login]];
					break;
				default:
					throw new \RuntimeException;
			}
			$this->identity = $query->where($condition)->one();
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
