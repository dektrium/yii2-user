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
    const VERSION = '0.6.0-dev';

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
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        foreach ($this->getModuleComponents() as $name => $component) {
            if (!isset($config['components'][$name])) {
                $config['components'][$name] = $component;
            } elseif (is_array($config['components'][$name]) && !isset($config['components'][$name]['class'])) {
                $config['components'][$name]['class'] = $component['class'];
            }
        }
        parent::__construct($id, $parent, $config);
    }

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
     * Returns module components.
     *
     * @return array
     */
    protected function getModuleComponents()
    {
        return [
            'factory' => [
                'class' => '\dektrium\user\Factory'
            ]
        ];
    }
}
