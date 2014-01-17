<?php namespace dektrium\user\models;

use yii\web\IdentityInterface;

interface UserInterface extends IdentityInterface
{
	/**
	 * Registers a user.
	 *
	 * @param  bool $generatePassword Whether to generate password for user automatically.
	 * @return bool
	 */
	public function register($generatePassword = false);

	/**
	 * Confirms a user by setting it's "confirmation_time" to actual time
	 *
	 * @return bool
	 */
	public function confirm();

	/**
	 * Sends confirmation instructions by email.
	 *
	 * @return bool
	 */
	public function sendConfirmationMessage();

	/**
	 * @return string Confirmation url.
	 */
	public function getConfirmationUrl();

	/**
	 * Verifies whether a user is confirmed or not.
	 *
	 * @return bool
	 */
	public function getIsConfirmed();

	/**
	 * Checks if the user confirmation happens before the token becomes invalid.
	 *
	 * @return bool
	 */
	public function getIsConfirmationPeriodExpired();

	/**
	 * Resets password and sets recovery token to null.
	 *
	 * @param $password
	 * @return bool
	 */
	public function reset($password);

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