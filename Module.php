<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user;

use yii\base\Module as BaseModule;
use yii\console\Application as ConsoleApplication;

/**
 * This is the main module class for the Dektrium user module.
 *
 * @property Factory $factory
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Module extends BaseModule
{
	const VERSION = '0.5.0-DEV';

	/**
	 * @var array Actions on which captcha will be shown.
	 */
	public $captcha = [];

	/**
	 * @var string Allowed types: 'email', 'username', 'both'
	 */
	public $loginType = 'email';

	/**
	 * @var bool Whether to allow login without confirmation.
	 */
	public $allowUnconfirmedLogin = false;

	/**
	 * @var int The time you want the user will be remembered without asking for credentials.
	 * By default rememberFor is two weeks.
	 */
	public $rememberFor = 1209600;

	/**
	 * @var bool Whether to generate user password automatically.
	 */
	public $generatePassword = false;

	/**
	 * @var bool Whether to track user's registration and sign in
	 */
	public $trackable = true;

	/**
	 * @var bool Whether confirmation needed
	 */
	public $confirmable = true;

	/**
	 * @var int The time before a sent confirmation token becomes invalid.
	 * By default confirmWithin is 24 hours.
	 */
	public $confirmWithin = 86400;

	/**
	 * @var bool Whether to enable password recovery.
	 */
	public $recoverable = true;

	/**
	 * @var int The time before a recovery token becomes invalid.
	 * By default recoverWithin is 6 hours.
	 */
	public $recoverWithin = 21600;

	/**
	 * @var int Cost parameter used by the Blowfish hash algorithm.
	 */
	public $cost = 10;

	/**
	 * @var string
	 */
	public $emailViewPath = '@dektrium/user/views/mail';

	/**
	 * @var array An array of administrator's usernames.
	 */
	public $admins = [];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		if (\Yii::$app instanceof ConsoleApplication) {
			$this->controllerNamespace = '\dektrium\user\commands';
			$this->setControllerPath(__DIR__.'/commands');
		}

		\Yii::$app->getI18n()->translations['user*'] = [
			'class' => 'yii\i18n\PhpMessageSource',
			'basePath' => __DIR__ . '/messages',
		];

		$this->setComponents([
			'factory' => [
				'class' => '\dektrium\user\Factory'
			]
		]);
	}
}
