<?php namespace dektrium\user\commands;

use dektrium\user\models\User;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * CleanController deletes unconfirmed users and out-of-dated tokens.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class CleanController extends Controller
{
	/**
	 * Deletes expired confirmation tokens.
	 */
	public function actionTokens()
	{
		if ($this->confirm('Are you sure?', true)) {
			/** @var User[] $users */
			$users = User::find()
						 ->where('confirmation_token IS NOT NULL')
						 ->orWhere('recovery_token IS NOT NULL')
						 ->all();
			foreach ($users as $user) {
				if (!$user->getIsConfirmed() && $user->getIsConfirmationPeriodExpired()) {
					$user->confirmation_token = null;
					$user->confirmation_sent_time = null;
				}
				if ($user->getIsRecoveryPeriodExpired()) {
					$user->recovery_token = null;
					$user->recovery_sent_time = null;
				}
				$user->save(false);
			}
			$this->stdout("Finished! All tokens have been deleted\n", Console::FG_GREEN);
		}
	}

	/**
	 * Deletes unconfirmed accounts.
	 *
	 * @param int $days
	 */
	public function actionUnconfirmed($days = 7)
	{
		if ($this->confirm('Are you sure?')) {
			$count = 0;
			/** @var User[] $users */
			$users = User::find()->where(['confirmation_time' => null])->all();
			foreach ($users as $user) {
				if (!$user->isConfirmed && $user->isConfirmationPeriodExpired && ($user->create_time + $days * 24 * 3600) < time()) {
					$user->delete();
					$count++;
				}
			}
			$this->stdout("Finished! $count users have been deleted.\n", Console::FG_GREEN);
		}
	}
}