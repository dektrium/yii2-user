<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user\models;

use yii\web\IdentityInterface;

interface UserInterface extends IdentityInterface
{
	/**
	 * Registers a user.
	 *
	 * @return bool
	 */
	public function register();

	/**
	 * Confirms a user by setting it's "confirmation_time" to actual time
	 *
	 * @return bool
	 */
	public function confirm();

	/**
	 * Re-sends confirmation instructions by email.
	 *
	 * @return bool
	 */
	public function resend();

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