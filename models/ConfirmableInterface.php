<?php namespace dektrium\user\models;

/**
 * Interface ConfirmableInterface
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
interface ConfirmableInterface
{
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
}