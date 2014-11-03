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

/**
 * This is the main module class for the Yii2-user.
 *
 * @property ModelManager $manager
 * @property Mailer       $mailer
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Module extends BaseModule
{
    const VERSION = '0.8.1';

    /**
     * Email is changed right after user enter's new email address.
     */
    const STRATEGY_INSECURE = 0;

    /**
     * Email is changed after user clicks confirmation link sent to his new email address.
     */
    const STRATEGY_DEFAULT = 1;

    /**
     * Email is changed after user clicks both confirmation links sent to his old and new email addresses.
     */
    const STRATEGY_SECURE = 2;

    /**
     * @var string Web user class.
     */
    public $webUserClass = 'yii\web\User';

    /**
     * @var bool Whether to enable registration.
     */
    public $enableRegistration = true;

    /**
     * @var bool Whether to remove password field from registration form.
     */
    public $enableGeneratingPassword = false;

    /**
     * @var bool Whether user has to confirm his account.
     */
    public $enableConfirmation = true;

    /**
     * @var bool Whether to allow logging in without confirmation.
     */
    public $enableUnconfirmedLogin = false;

    /**
     * @var bool Whether to enable password recovery.
     */
    public $enablePasswordRecovery = true;

    /**
     * @var integer Email changing strategy.
     */
    public $emailChangeStrategy = self::STRATEGY_DEFAULT;

    /**
     * @var int The time you want the user will be remembered without asking for credentials.
     */
    public $rememberFor = 1209600; // two weeks

    /**
     * @var int The time before a confirmation token becomes invalid.
     */
    public $confirmWithin = 86400; // 24 hours

    /**
     * @var int The time before a recovery token becomes invalid.
     */
    public $recoverWithin = 21600; // 6 hours

    /**
     * @var int Cost parameter used by the Blowfish hash algorithm.
     */
    public $cost = 10;

    /**
     * @var array An array of administrator's usernames.
     */
    public $admins = [];

    /**
     * @var string The prefix for user module URL.
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'user';

    /**
     * @var array The rules to be used in URL management.
     */
    public $urlRules = [
        '<id:\d+>'                     => 'profile/show',
        '<action:(login|logout)>'      => 'security/<action>',
        '<action:(register|resend)>'   => 'registration/<action>',
        'confirm/<id:\d+>/<token:\w+>' => 'registration/confirm',
        'forgot'                       => 'recovery/request',
        'recover/<id:\d+>/<token:\w+>' => 'recovery/reset',
        'settings/<action:\w+>'        => 'settings/<action>'
    ];

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
     * Returns module components.
     * @return array
     */
    protected function getModuleComponents()
    {
        return [
            'manager' => [
                'class' => 'dektrium\user\ModelManager'
            ],
            'mailer' => [
                'class' => 'dektrium\user\Mailer'
            ]
        ];
    }
}
