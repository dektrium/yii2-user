<?php namespace dektrium\user\models;

use yii\db\ActiveRecord;
use yii\helpers\Security;

/**
 * User ActiveRecord model.
 *
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $password_hash
 * @property string  $auth_key
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property integer $registration_ip
 * @property integer $login_ip
 * @property integer $login_time
 *
 * @property string  $confirmation_token
 * @property integer $confirmation_sent_time
 * @property integer $confirmation_time
 *
 * @property string  $recovery_token
 * @property integer $recovery_sent_time
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements UserInterface
{
	/**
	 * @var string Plain password. Used for model validation.
	 */
	public $password;

	/**
	 * @var \dektrium\user\Module
	 */
	private $_module;

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		return [
			'register' => ['username', 'email', 'password'],
			'reset'    => ['password'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['username', 'email', 'password'], 'required', 'on' => ['register']],
			['email', 'email'],
			[['username', 'email'], 'unique'],
			['username', 'match', 'pattern' => '/^[a-zA-Z]\w+$/'],
			['username', 'string', 'min' => 3, 'max' => 25],
			['email', 'string', 'max' => 255],
			['password', 'string', 'min' => 6],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if ($this->isAttributeSafe('password') && !empty($this->password)) {
				$this->setAttribute('password_hash', Security::generatePasswordHash($this->password, $this->getModule()->cost));
			}
			if ($this->isNewRecord) {
				$this->setAttribute('auth_key', Security::generateRandomKey());
				$this->setAttribute('create_time', time());
			}
			$this->setAttribute('update_time', time());

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Finds a user by username.
	 *
	 * @param $username
	 * @return null|User
	 */
	public static function findByUsername($username)
	{
		return static::find(['username' => $username]);
	}

	/**
	 * Finds a user by email.
	 *
	 * @param $email
	 * @return null|User
	 */
	public static function findByEmail($email)
	{
		return static::find(['email' => $email]);
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user}}';
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id)
	{
		return static::find($id);
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->getAttribute('id');
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey()
	{
		return $this->getAttribute('auth_key');
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey)
	{
		return $this->getAttribute('auth_key') == $authKey;
	}


	/**
	 * Registers a user.
	 *
	 * @param  bool $generatePassword Whether to generate password for user automatically.
	 * @return bool
	 * @throws \RuntimeException
	 */
	public function register($generatePassword = false)
	{
		if (!$this->getIsNewRecord()) {
			throw new \RuntimeException('Calling "'.__CLASS__.'::register()" on existing user');
		}

		if ($generatePassword) {
			$password = $this->generatePassword(8);
			$this->password = $password;
			$html = \Yii::$app->getView()->renderFile($this->getModule()->welcomeMessageView, [
				'user' => $this,
				'password' => $password
			]);
			\Yii::$app->getMail()->compose()
					  ->setTo($this->email)
					  ->setFrom($this->getModule()->messageSender)
					  ->setSubject($this->getModule()->welcomeMessageSubject)
					  ->setHtmlBody($html)
					  ->send();
		}

		return $this->save();
	}

	/**
	 * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
	 * digit. The remaining characters in the password are chosen at random from those four sets.
	 * @see https://gist.github.com/tylerhall/521810
	 * @param $length
	 * @return string
	 */
	protected function generatePassword($length)
	{
		$sets = [
			'abcdefghjkmnpqrstuvwxyz',
			'ABCDEFGHJKMNPQRSTUVWXYZ',
			'23456789'
		];
		$all = '';
		$password = '';
		foreach($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for ($i = 0; $i < $length - count($sets); $i++) {
			$password .= $all[array_rand($all)];
		}

		$password = str_shuffle($password);

		return $password;
	}

	/**
	 * Confirms a user by setting it's "confirmation_time" to actual time
	 *
	 * @return bool
	 */
	public function confirm()
	{
		if ($this->getIsConfirmed()) {
			return true;
		} elseif ($this->getIsConfirmationPeriodExpired()) {
			return false;
		}

		$this->confirmation_token = null;
		$this->confirmation_sent_time = null;
		$this->confirmation_time = time();

		return $this->save(false);
	}

	/**
	 * Sends confirmation instructions by email.
	 *
	 * @return bool
	 */
	public function sendConfirmationMessage()
	{
		$this->generateConfirmationData();
		$html = \Yii::$app->getView()->renderFile($this->getModule()->confirmationMessageView, ['user' => $this]);
		\Yii::$app->getMail()->compose()
				  ->setTo($this->email)
				  ->setFrom($this->getModule()->messageSender)
				  ->setSubject($this->getModule()->confirmationMessageSubject)
				  ->setHtmlBody($html)
				  ->send();
	}

	/**
	 * @return string Confirmation url.
	 */
	public function getConfirmationUrl()
	{
		return $this->getIsConfirmed() ? null :
			\Yii::$app->getUrlManager()->createAbsoluteUrl('/user/registration/confirm', [
				'id'    => $this->id,
				'token' => $this->confirmation_token
			]);
	}

	/**
	 * Verifies whether a user is confirmed or not.
	 *
	 * @return bool
	 */
	public function getIsConfirmed()
	{
		return $this->confirmation_time !== null;
	}

	/**
	 * Checks if the user confirmation happens before the token becomes invalid.
	 *
	 * @return bool
	 */
	public function getIsConfirmationPeriodExpired()
	{
		return $this->confirmation_sent_time != null && ($this->confirmation_sent_time + $this->getModule()->confirmWithin) < time();
	}

	/**
	 * Generates confirmation data.
	 */
	protected function generateConfirmationData()
	{
		$this->confirmation_token = Security::generateRandomKey();
		$this->confirmation_sent_time = time();
		$this->confirmation_time = null;
		$this->save(false);
	}

	/**
	 * Resets password and sets recovery token to null
	 *
	 * @param  string $password
	 * @return bool
	 */
	public function reset($password)
	{
		$this->scenario = 'reset';
		$this->password = $password;
		$this->recovery_token = null;
		$this->recovery_sent_time = null;

		return $this->save(false);
	}

	/**
	 * Checks if the password recovery happens before the token becomes invalid.
	 *
	 * @return bool
	 */
	public function getIsRecoveryPeriodExpired()
	{
		return ($this->recovery_sent_time + $this->getModule()->recoverWithin) < time();
	}

	/**
	 * @return string Recovery url
	 */
	public function getRecoveryUrl()
	{
		return \Yii::$app->getUrlManager()->createAbsoluteUrl('/user/recovery/reset', [
			'id' => $this->id,
			'token' => $this->recovery_token
		]);
	}

	/**
	 * Sends recovery message to user.
	 */
	public function sendRecoveryMessage()
	{
		$this->generateRecoveryData();
		$html = \Yii::$app->getView()->renderFile($this->getModule()->recoveryMessageView, ['user' => $this]);
		\Yii::$app->getMail()->compose()
				  ->setTo($this->email)
				  ->setFrom($this->getModule()->messageSender)
				  ->setSubject($this->getModule()->recoveryMessageSubject)
				  ->setHtmlBody($html)
				  ->send();
		\Yii::$app->getSession()->setFlash('recovery_message_sent');
	}

	/**
	 * Generates recovery data.
	 */
	protected function generateRecoveryData()
	{
		$this->recovery_token = Security::generateRandomKey();
		$this->recovery_sent_time = time();
		$this->save(false);
	}

	/**
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		if ($this->_module == null) {
			$this->_module = \Yii::$app->getModule('user');
		}

		return $this->_module;
	}
}