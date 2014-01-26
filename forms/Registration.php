<?php namespace dektrium\user\forms;

use dektrium\user\models\UserInterface;
use yii\base\Model;

/**
 * Registration form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Registration extends Model
{
	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var string
	 */
	public $verifyCode;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'username'   => \Yii::t('user', 'Username'),
			'email'      => \Yii::t('user', 'Email'),
			'password'   => \Yii::t('user', 'Password'),
			'verifyCode' => \Yii::t('user', 'Verification Code'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		$attributes = $this->getModule()->generatePassword ? ['username', 'email'] : ['username', 'email', 'password'];
		if (in_array('register', $this->getModule()->captcha)) {
			$attributes[] = 'verifyCode';
		}
		return [
			'default' => $attributes
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = [
			['email', 'email'],
			[['username', 'email'], 'unique', 'targetClass' => $this->getModule()->factory->modelClass],
			['username', 'match', 'pattern' => '/^[a-zA-Z]\w+$/'],
			['username', 'string', 'min' => 3, 'max' => 25],
			['email', 'string', 'max' => 255],
		];

		if ($this->getModule()->generatePassword) {
			$rules[] = [['username', 'email'], 'required'];
		} else {
			$rules[] = [['username', 'email', 'password'], 'required'];
			$rules[] = ['password', 'string', 'min' => 6];
		}

		if (in_array('register', $this->getModule()->captcha)) {
			$rules[] = ['verifyCode', 'captcha', 'captchaAction' => 'user/registration/captcha'];
		}

		return $rules;
	}

	/**
	 * Register a user
	 */
	public function register()
	{
		if ($this->validate()) {
			$identity = $this->getModule()->factory->createUser('register');
			$identity->scenario = 'register';
			$identity->setAttributes([
				'username' => $this->username,
				'email' => $this->email,
			]);
			if (!$this->getModule()->generatePassword) {
				$identity->password = $this->password;
			}
			if ($this->getModule()->trackable) {
				$identity->registration_ip = \Yii::$app->getRequest()->getUserIP();
			}
			if ($identity->register($this->getModule()->generatePassword)) {
				if ($this->getModule()->confirmable) {
					$identity->sendConfirmationMessage();
					\Yii::$app->getSession()->setFlash('confirmation_message_sent');
				}
				return true;
			}
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function formName()
	{
		return 'registration-form';
	}

	/**
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		return \Yii::$app->getModule('user');
	}
}