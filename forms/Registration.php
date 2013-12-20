<?php namespace dektrium\user\forms;

use yii\base\Model;

class Registration extends Model
{
	public $username;
	public $email;
	public $password;
	public $verifyCode;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'username' => \Yii::t('user', 'Username'),
			'email' => \Yii::t('user', 'Email'),
			'password' => \Yii::t('user', 'Password'),
			'verifyCode' => \Yii::t('user', 'Verification Code'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['username', 'email', 'password'], 'required'],
			['email', 'email'],
			[['username', 'email'], 'unique', 'className' => '\dektrium\user\models\User'],
			['username', 'match', 'pattern' => '/^[a-zA-Z]\w+$/'],
			['username', 'string', 'min' => 3, 'max' => 25],
			['email', 'string', 'max' => 255],
			['password', 'string', 'min' => 6],
			['verifyCode', 'captcha', 'skipOnEmpty' => !in_array('register', \Yii::$app->getModule('user')->captcha)]
		];
	}

	/**
	 * Register a user
	 */
	public function register()
	{
		if ($this->validate()) {
			/** @var \dektrium\user\models\User $user */
			$user = \Yii::createObject([
				'class' => \Yii::$app->getUser()->identityClass
			]);
			$user->scenario = 'register';
			$user->setAttributes([
				'username' => $this->username,
				'email' => $this->email,
				'password' => $this->password
			]);
			if ($user->register()) {
				if (\Yii::$app->getModule('user')->confirmable) {
					$user->sendConfirmationMessage();
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function formName()
	{
		return 'registration-form';
	}
}