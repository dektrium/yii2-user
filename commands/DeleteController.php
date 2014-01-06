<?php namespace dektrium\user\commands;

use dektrium\user\models\User;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * DeleteController allows you to delete user accounts.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class DeleteController extends Controller
{
	/**
	 * Deletes a user by email.
	 *
	 * @param string $email
	 */
	public function actionIndex($email)
	{
		if ($this->confirm('Are you sure? Deleted user can not be restored!')) {
			$user = User::findByEmail($email);
			if ($user === null) {
				$this->stdout("User is not found!\n", Console::FG_RED);
			} else {
				if ($user->delete()) {
					$this->stdout("User has been deleted!\n", Console::FG_GREEN);
				} else {
					$this->stdout("Error occurred while deleting user\n", Console::FG_RED);
				}
			}
		}
	}
}