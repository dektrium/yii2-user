<?php namespace dektrium\user\models;

use yii\helpers\Security;

/**
 * Recoverable is responsible for resetting the user password and send reset instructions.
 *
 * @property string  $recovery_token
 * @property integer $recovery_sent_time
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait RecoverableTrait
{
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
	 * @throws \RuntimeException Whether dektrium\user\Module.recoverable is false.
	 */
	public function getIsRecoveryPeriodExpired()
	{
		return ($this->recovery_sent_time + \Yii::$app->getModule('user')->recoverWithin) < time();
	}

	/**
	 * @return string Recovery url
	 * @throws \RuntimeException Whether dektrium\user\Module.recoverable is false.
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
	 * @throws \RuntimeException Whether dektrium\user\Module.recoverable is false.
	 */
	protected function generateRecoveryData()
	{
		$this->recovery_token = Security::generateRandomKey();
		$this->recovery_sent_time = time();
		$this->save(false);
	}

	/**
	 * @return \dektrium\user\Module
	 */
	abstract protected function getModule();
}