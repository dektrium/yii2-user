<?php namespace dektrium\user\models;

use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\Security;
use yii\web\IdentityInterface;

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
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface, RegisterableInterface, ConfirmableInterface, RecoverableInterface
{
	use RegisterableTrait;
	use ConfirmableTrait;
	use RecoverableTrait;

	/**
	 * @var string Plain password. Used for model validation.
	 */
	public $password;

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
	public function scenarios()
	{
		return [
			'register' => ['username', 'email', 'password'],
			'reset' => ['password'],
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
	public static function tableName()
	{
		return '{{user}}';
	}

	/**
	 * @inheritdoc
	 */
	public function formName()
	{
		return 'user-' . Inflector::camel2id($this->scenario) . '-form';
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
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			if ($this->isAttributeSafe('password') && !empty($this->password)) {
				$this->setAttribute('password_hash', Security::generatePasswordHash($this->password, \Yii::$app->getModule('user')->cost));
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
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		return \Yii::$app->getModule('user');
	}
}