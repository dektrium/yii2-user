<?php namespace dektrium\user\forms;

use dektrium\user\models\RecoverableInterface;
use yii\base\Model;
use yii\db\ActiveQuery;

/**
 * Recovery form manages requesting recovery token and resetting password.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
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
	 * @var RecoverableInterface
	 */
	private $_identity;

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
		$attributes = ['email'];
		if (in_array('recovery', $this->getModule()->captcha)) {
			$attributes[] = 'verifyCode';
		}

		return [
			'request' => $attributes,
			'reset' => ['password']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = [
			['email', 'required', 'on' => 'request'],
			['email', 'email', 'on' => 'request'],
			['email', 'exist', 'className' => \Yii::$app->getUser()->identityClass, 'on' => 'request'],
			['email', 'validateUserConfirmed', 'on' => 'request'],
			['password', 'required', 'on' => 'reset'],
			['password', 'string', 'min' => 6, 'on' => 'reset'],
		];

		if (in_array('recovery', $this->getModule()->captcha)) {
			$rules[] = ['verifyCode', 'captcha', 'on' => 'request'];
		}

		return $rules;
	}

	/**
	 * Validates that user has confirmed email.
	 */
	public function validateUserConfirmed()
	{
		$query = new ActiveQuery(['modelClass' => \Yii::$app->getUser()->identityClass]);
		$this->identity = $query->where(['email' => $this->email])->one();
		if ($this->identity !== null && $this->getModule()->confirmable && !$this->identity->isConfirmed) {
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
		if ($this->validate() && $this->identity->reset($this->password)) {
			\Yii::$app->getSession()->setFlash('password_reset');
			return true;
		}

		return false;
	}

	/**
	 * @return RecoverableInterface
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}

	/**
	 * @param RecoverableInterface $identity
	 */
	public function setIdentity(RecoverableInterface $identity)
	{
		$this->identity = $identity;
	}

	/**
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		return \Yii::$app->getModule('user');
	}
}