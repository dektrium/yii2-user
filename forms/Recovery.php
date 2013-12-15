<?php namespace dektrium\user\forms;

use dektrium\user\models\User;
use yii\base\Model;
use yii\db\ActiveQuery;

class Recovery extends Model
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
	 * @var string
	 */
	public $verifyCode;

	/**
	 * @var User
	 */
	protected $identity;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
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
			'request' => ['email', 'verifyCode'],
			'reset' => ['password']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['email', 'required', 'on' => 'request'],
			['email', 'email', 'on' => 'request'],
			['email', 'exist', 'className' => '\dektrium\user\models\User', 'on' => 'request'],
			['email', 'validateUserConfirmed', 'on' => 'request'],
			['password', 'required', 'on' => 'reset'],
			['password', 'string', 'min' => 6, 'on' => 'reset'],
			['verifyCode', 'captcha', 'skipOnEmpty' => !in_array('recovery', \Yii::$app->getModule('user')->captcha)]
		];
	}

	/**
	 * Validates that user has confirmed email.
	 */
	public function validateUserConfirmed()
	{
		$query = new ActiveQuery(['modelClass' => \Yii::$app->getUser()->identityClass]);
		$this->identity = $query->where(['email' => $this->email])->one();
		if ($this->identity !== null && \Yii::$app->getModule('user')->confirmable && !$this->identity->isConfirmed) {
			$this->addError('email', 'You must confirm your account first');
		}
	}

	/**
	 * Validates form and sends recovery message to user.
	 *
	 * @return bool
	 */
	public function sendRecoveryMessage()
	{
		if ($this->validate() && $this->scenario == 'request') {
			$this->identity->sendRecoveryMessage();
			\Yii::$app->getSession()->setFlash('recovery_message_sent');
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
		return 'recovery-form';
	}

	/**
	 * Resets user's password.
	 *
	 * @return bool
	 */
	public function reset()
	{
		if ($this->validate()) {
			$this->identity->scenario = 'reset';
			$this->identity->password = $this->password;
			$this->identity->recovery_token = null;
			$this->identity->recovery_sent_time = null;
			if ($this->identity->save()) {
				\Yii::$app->getSession()->setFlash('password_reset');
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * @param User $user
	 */
	public function setIdentity(User $user)
	{
		$this->identity = $user;
	}
}