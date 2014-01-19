<?php namespace dektrium\user\forms;

use dektrium\user\models\UserInterface;
use yii\base\Model;
use yii\db\ActiveQuery;

/**
 * Model that manages resending confirmation tokens to users.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Resend extends Model
{
	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var string
	 */
	public $verifyCode;

	/**
	 * @var \dektrium\user\models\UserInterface
	 */
	private $_identity;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'email' => \Yii::t('user', 'Email'),
			'verifyCode' => \Yii::t('user', 'Verification Code'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = [
			['email', 'required'],
			['email', 'email'],
			['email', 'exist', 'targetClass' => $this->getModule()->factory->modelClass],
			['email', 'validateEmail'],
		];

		if (in_array('resend', $this->getModule()->captcha)) {
			$rules[] = ['verifyCode', 'captcha'];
		}

		return $rules;
	}

	/**
	 * Validates if user has already been confirmed or not.
	 */
	public function validateEmail()
	{
		if ($this->getIdentity() != null && $this->getIdentity()->getIsConfirmed()) {
			$this->addError('email', \Yii::t('user', 'This account has already been confirmed'));
		}
	}

	/**
	 * Resends confirmation message to user.
	 *
	 * @return bool
	 */
	public function resend()
	{
		if ($this->validate()) {
			$this->getIdentity()->sendConfirmationMessage();
			\Yii::$app->getSession()->setFlash('confirmation_message_sent');
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
		return 'resend-form';
	}

	/**
	 * @return \dektrium\user\models\UserInterface
	 * @throws \RuntimeException
	 */
	protected function getIdentity()
	{
		if ($this->_identity == null) {
			$query = $this->getModule()->factory->createQuery();
			$this->_identity = $query->where(['email' => $this->email])->one();
		}

		return $this->_identity;
	}

	/**
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		return \Yii::$app->getModule('user');
	}
}
