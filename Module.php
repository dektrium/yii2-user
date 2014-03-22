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
    const VERSION = '0.5.1';

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
     * @var string|null Role that will be assigned to user on creation.
     */
    public $defaultRole = null;

    /**
     * @var array An array of administrator's usernames.
     */
    public $admins = [];

    /**
     * @var Factory
     */
    private $_factory = ['class' => '\dektrium\user\Factory'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (\Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'dektrium\user\commands';
        }

        \Yii::$app->getI18n()->translations['user*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => __DIR__ . '/messages',
        ];
    }

    /**
     * @return Factory
     */
    public function getFactory()
    {
        if (is_array($this->_factory)) {
            $this->_factory = \Yii::createObject($this->_factory);
        }

        return $this->_factory;
    }

    /**
     * @param $config
     */
    public function setFactory(array $config)
    {
        if (!isset($config['class'])) {
            $config['class'] = '\dektrium\user\Factory';
        }
        $this->_factory = $config;
    }
}
