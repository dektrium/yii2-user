<?php namespace dektrium\user;

use \yii\base\Module;

/**
 * This is the main module class for the Dektrium user module.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class WebModule extends Module
{
    /**
     * @var string the namespace that controller classes are in.
     */
    public $controllerNamespace = '\dektrium\user\controllers';

    /**
     * @var int The time you want the user will be remembered without asking for credentials.
     * By default rememberFor is two weeks.
     */
    public $rememberFor = 1209600;

    /**
     * @var array Url where the user will be redirected to after registration.
     */
    public $registrationRedirectUrl = ['/user/auth/login'];

	/**
	 * @var bool Whether to enable "Trackable" behavior.
	 */
	public $trackable = false;

	/**
	 * @var bool Whether to enable "Trackable" behavior.
	 */
	public $confirmable = false;

	/**
	 * @var bool Whether to allow login without confirmation.
	 */
	public $allowUnconfirmedLogin = false;

	/**
	 * @var int The time before a sent confirmation token becomes invalid.
	 */
	public $confirmWithin = 86400;
}