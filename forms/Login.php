<?php namespace dektrium\user\forms;

use yii\base\Model;
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
	protected $user;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		switch ($this->getModule()->loginType) {
			case 'email':
				$loginLabel = \Yii::t('user', 'Email');
				break;
			case 'username':
				$loginLabel = \Yii::t('user', 'Username');
				break;
			case 'both':
				$loginLabel = \Yii::t('user', 'Email or username');
				break;
			default:
				throw new \RuntimeException;
		}

		return [
			'login'      => $loginLabel,
			'password'   => \Yii::t('user', 'Password'),
			'rememberMe' => \Yii::t('user', 'Remember me next time'),
			'verifyCode' => \Yii::t('user', 'Verification Code'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = [
			[['login', 'password'], 'required'],
			['password', 'validatePassword'],
			['login', 'validateUserActiveForLogin'],
			['rememberMe', 'boolean'],
		];

		if (in_array('login', $this->getModule()->captcha)) {
			$rules[] = ['verifyCode', 'captcha', 'captchaAction' => 'user/default/captcha'];
		}

		return $rules;
	}

	/**
	 * Validates the password.
	 */
	public function validatePassword()
	{
		if ($this->user === null || !Security::validatePassword($this->password, $this->user->password_hash)) {
			$this->addError('password', \Yii::t('user', 'Invalid login or password'));
		}
	}

	/**
	 * Validates whether user has confirmed his account.
	 */
	public function validateUserActiveForLogin()
	{
		$confirmationRequired = $this->getModule()->confirmable && !$this->getModule()->allowUnconfirmedLogin;
		if ($this->user !== null && $confirmationRequired && !$this->user->isConfirmed) {
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
			$query = $this->getModule()->factory->createQuery();
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
					throw new \RuntimeException('Unknown login type');
			}
			$this->user = $query->where($condition)->one();
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
