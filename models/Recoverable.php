<?php namespace dektrium\user\models;

use yii\helpers\Security;

/**
 * Recoveralbe is responsible to reset the user password and send reset instructions.
 *
 * @property string  $recovery_token
 * @property integer $recovery_sent_time
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait Recoverable
{
	/**
	 * Checks if the password recovery happens before the token becomes invalid.
	 *
	 * @return bool
	 * @throws \RuntimeException Whether dektrium\user\Module.recoverable is false.
	 */
	public function getIsRecoveryPeriodExpired()
	{
		if (!\Yii::$app->getModule('user')->recoverable) {
			throw new \RuntimeException('You must enable dektrium\user\Module.recoverable to use method this method');
		}
		return ($this->recovery_sent_time + \Yii::$app->getModule('user')->recoverWithin) < time();
	}

	/**
	 * @return string Recovery url
	 * @throws \RuntimeException Whether dektrium\user\Module.recoverable is false.
	 */
	public function getRecoveryUrl()
	{
		if (!\Yii::$app->getModule('user')->recoverable) {
			throw new \RuntimeException('You must enable dektrium\user\Module.recoverable to use method this method');
		}
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
		$html = \Yii::$app->getView()->renderFile(\Yii::$app->getModule('user')->recoveryMessageView, [
			'user' => $this
		]);
		\Yii::$app->getMail()->compose()
				  ->setTo($this->email)
				  ->setFrom(\Yii::$app->getModule('user')->messageSender)
				  ->setSubject(\Yii::$app->getModule('user')->recoveryMessageSubject)
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
		if (!\Yii::$app->getModule('user')->recoverable) {
			throw new \RuntimeException('You must enable dektrium\user\Module.recoverable to use method this method');
		}
		$this->recovery_token = Security::generateRandomKey();
		$this->recovery_sent_time = time();
		$this->save(false);
	}
}