<?php namespace dektrium\user;

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
	const VERSION = '0.4.0-DEV';

	/**
	 * @var array Actions on which captcha will be shown.
	 */
	public $captcha = [];

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
	 * @var bool Whether to enable "Trackable" behavior.
	 */
	public $trackable = false;

	/**
	 * @var bool Whether to enable "Confirmable" behavior.
	 */
	public $confirmable = true;

	/**
	 * @var bool Whether to allow login without confirmation.
	 */
	public $allowUnconfirmedLogin = false;

	/**
	 * @var int The time before a sent confirmation token becomes invalid.
	 * By default confirmWithin is 24 hours.
	 */
	public $confirmWithin = 86400;

	/**
	 * @var bool Whether to enable "Recoverable" behavior.
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
	 * @var string
	 */
	protected $loginType = 'email';

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

	/**
	 * Sets login type. Allowed types: 'email', 'username', 'both'
	 *
	 * @param $value
	 * @throws \InvalidArgumentException
	 */
	public function setLoginType($value)
	{
		$allowed = ['email', 'username', 'both'];
		if (!in_array($value, $allowed)) {
			throw new \InvalidArgumentException('Setting unknown login type');
		}
		$this->loginType = $value;
	}

	/**
	 * @return string Login type
	 */
	public function getLoginType()
	{
		return $this->loginType;
	}
}
