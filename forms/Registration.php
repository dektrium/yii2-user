<?php namespace dektrium\user\forms;

use dektrium\user\models\ConfirmableInterface;
use dektrium\user\models\RegisterableInterface;
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
			['verifyCode', 'captcha', 'skipOnEmpty' => !in_array('register', $this->getModule()->captcha)]
		];
	}

	/**
	 * Register a user
	 */
	public function register()
	{
		if ($this->validate()) {
			$identity = $this->getIdentity();
			$identity->scenario = 'register';
			$identity->setAttributes([
				'username' => $this->username,
				'email' => $this->email,
				'password' => $this->password
			]);
			if ($identity->register()) {
				if ($this->getModule()->confirmable) {
					$identity->sendConfirmationMessage();
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
	 * @return \dektrium\user\models\User
	 * @throws \RuntimeException
	 */
	protected function getIdentity()
	{
		$identity = \Yii::createObject([
			'class' => \Yii::$app->getUser()->identityClass
		]);
		if (!$identity instanceof RegisterableInterface) {
			throw new \RuntimeException(sprintf('"%s" must implement "%s" interface',
				get_class($identity), '\dektrium\user\models\RegisterableInterface'));
		} elseif ($this->getModule()->confirmable && !$identity instanceof ConfirmableInterface) {
			throw new \RuntimeException(sprintf('"%s" must implement "%s" interface',
				get_class($identity), '\dektrium\user\models\ConfirmableInterface'));
		}

		return $identity;
	}

	/**
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		return \Yii::$app->getModule('user');
	}
}