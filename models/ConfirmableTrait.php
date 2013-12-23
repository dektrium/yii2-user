<?php namespace dektrium\user\models;

use yii\helpers\Security;

/**
 * ConfirmableTrait is responsible for confirmation of user accounts.
 *
 * @property string  $confirmation_token
 * @property integer $confirmation_sent_time
 * @property integer $confirmation_time
 * @property boolean $isConfirmed
 * @property boolean $isConfirmationPeriodExpired
 */
trait ConfirmableTrait
{
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
		\Yii::$app->getSession()->setFlash('confirmation_message_sent');
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
		return ($this->confirmation_sent_time + $this->getModule()->confirmWithin) < time();
	}

	protected function generateConfirmationData()
	{
		$this->confirmation_token = Security::generateRandomKey();
		$this->confirmation_sent_time = time();
		$this->confirmation_time = null;
		$this->save(false);
	}

	/**
	 * @return \dektrium\user\Module
	 */
	abstract protected function getModule();
}