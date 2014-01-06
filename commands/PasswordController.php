<?php namespace dektrium\user\commands;

use dektrium\user\models\User;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * PasswordController allows you to change user's passwords.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class PasswordController extends Controller
{
	/**
	 * Changes user's password to given.
	 *
	 * @param string $email
	 * @param string $password
	 */
	public function actionIndex($email, $password)
	{
		$user = User::findByEmail($email);
		if ($user === null) {
			$this->stdout("User is not found!\n", Console::FG_RED);
		} else {
			if ($user->reset($password)) {
				$this->stdout("Password has been changed!\n", Console::FG_GREEN);
			} else {
				$this->stdout("Error occurred while changing password\n", Console::FG_RED);
			}
		}
	}
}