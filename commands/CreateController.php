<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user\commands;

use dektrium\user\models\User;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * CreateController allows you to create user accounts.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class CreateController extends Controller
{
	/**
	 * @var bool Whether user should confirm account.
	 */
	public $confirmation = false;

	/**
	 * Creates new user.
	 *
	 * @param string      $email
	 * @param string      $username
	 * @param null|string $password If null password will be generated automatically
	 */
	public function actionIndex($email, $username, $password = null)
	{
		$user = new User(['scenario' => 'register']);
		$user->setAttributes([
			'email'    => $email,
			'username' => $username,
			'password' => $password
		]);
		if ($user->register(is_null($password))) {
			$this->stdout("User has been created!\n", Console::FG_GREEN);
			if ($this->confirmation) {
				$user->sendConfirmationMessage();
				$this->stdout("Confirmation message has been sent!\n", Console::FG_GREEN);
			} else {
				// TODO: use 'confirm' method here
				$user->confirmation_time = time();
				$user->save(false);
			}
		} else {
			$this->stdout("Please fix following errors:\n", Console::FG_RED);
			foreach ($user->errors as $errors) {
				foreach ($errors as $error) {
					$this->stdout(" - ".$error."\n", Console::FG_RED);
				}
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function globalOptions()
	{
		return array_merge(parent::globalOptions(), ['confirmation']);
	}
}