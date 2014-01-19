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
	 * @var string View that will be rendered by Mailer.compose().
	 */
	public $welcomeMessageView = '@user/views/mail/welcome.php';

	/**
	 * @var string Subject of confirmation message.
	 */
	public $welcomeMessageSubject = 'Welcome to Site.com';

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
	 * @var string View that will be rendered by Mailer.compose().
	 */
	public $confirmationMessageView = '@user/views/mail/confirmation.php';

	/**
	 * @var string Subject of confirmation message.
	 */
	public $confirmationMessageSubject = 'Account confirmation on Site.com';

	/**
	 * @var string|array Message sender.
	 */
	public $messageSender = 'no-reply@example.com';

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
	 * @var string View that will be rendered by Mailer.compose() on password recovery.
	 */
	public $recoveryMessageView = '@user/views/mail/recovery.php';

	/**
	 * @var string Subject of recovery message.
	 */
	public $recoveryMessageSubject = 'Password recovery on Site.com';

	/**
	 * @var int Cost parameter used by the Blowfish hash algorithm.
	 */
	public $cost = 10;

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
		\Yii::$app->getI18n()->translations['user*'] = [
			'class' => 'yii\i18n\PhpMessageSource',
			'basePath' => __DIR__ . '/messages',
		];
		$this->setAliases([
			'@user' => __DIR__
		]);
		if (\Yii::$app instanceof ConsoleApplication) {
			$this->controllerNamespace = '\dektrium\user\commands';
			$this->setControllerPath(__DIR__.'/commands');
		}
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
