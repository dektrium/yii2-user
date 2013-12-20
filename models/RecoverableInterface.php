<?php namespace dektrium\user\models;

/**
 * Interface RecoverableInterface
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
interface RecoverableInterface
{
	/**
	 * Sends recovery password instructions by email.
	 *
	 * @return bool
	 */
	public function sendRecoveryMessage();

	/**
	 * @return string Recovery url
	 */
	public function getRecoveryUrl();

	/**
	 * Checks if the password recovery happens before the token becomes invalid.
	 *
	 * @return bool
	 */
	public function getIsRecoveryPeriodExpired();
}