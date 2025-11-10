<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User;

use Yii;
use yii\authclient\Collection;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{
    /** @var array Model's map */
    private $_modelMap = [
        'User' => 'AlexeiKaDev\Yii2User\models\User',
        'Account' => 'AlexeiKaDev\Yii2User\models\Account',
        'Profile' => 'AlexeiKaDev\Yii2User\models\Profile',
        'Token' => 'AlexeiKaDev\Yii2User\models\Token',
        'RegistrationForm' => 'AlexeiKaDev\Yii2User\models\RegistrationForm',
        'ResendForm' => 'AlexeiKaDev\Yii2User\models\ResendForm',
        'LoginForm' => 'AlexeiKaDev\Yii2User\models\LoginForm',
        'SettingsForm' => 'AlexeiKaDev\Yii2User\models\SettingsForm',
        'RecoveryForm' => 'AlexeiKaDev\Yii2User\models\RecoveryForm',
        'UserSearch' => 'AlexeiKaDev\Yii2User\models\UserSearch',
    ];

    /** @inheritdoc */
    public function bootstrap($app): void
    {
        /** @var Module $module */
        /** @var \yii\db\ActiveRecord $modelName */
        if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module) {
            $this->_modelMap = array_merge($this->_modelMap, $module->modelMap);

            foreach ($this->_modelMap as $name => $definition) {
                $class = "AlexeiKaDev\\Yii2User\\models\\" . $name;
                Yii::$container->set($class, $definition);
                $modelName = is_array($definition) ? $definition['class'] : $definition;
                $module->modelMap[$name] = $modelName;

                if (in_array($name, ['User', 'Profile', 'Token', 'Account'])) {
                    Yii::$container->set($name . 'Query', function () use ($modelName) {
                        return $modelName::find();
                    });
                }
            }

            Yii::$container->setSingleton(Finder::class, [
                'userQuery' => Yii::$container->get('UserQuery'),
                'profileQuery' => Yii::$container->get('ProfileQuery'),
                'tokenQuery' => Yii::$container->get('TokenQuery'),
                'accountQuery' => Yii::$container->get('AccountQuery'),
            ]);

            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'AlexeiKaDev\Yii2User\commands';
            } else {
                Yii::$container->set('yii\web\User', [
                    'enableAutoLogin' => true,
                    'loginUrl' => ['/user/security/login'],
                    'identityClass' => $module->modelMap['User'],
                ]);

                $configUrlRule = [
                    'prefix' => $module->urlPrefix,
                    'rules' => $module->urlRules,
                ];

                if ($module->urlPrefix != 'user') {
                    $configUrlRule['routePrefix'] = 'user';
                }

                $configUrlRule['class'] = 'yii\web\GroupUrlRule';
                $rule = Yii::createObject($configUrlRule);

                $app->urlManager->addRules([$rule], false);

                if (!$app->has('authClientCollection')) {
                    $app->set('authClientCollection', [
                        'class' => Collection::class,
                    ]);
                }
            }

            if (!isset($app->get('i18n')->translations['user*']) && !isset($app->get('i18n')->translations['AlexeiKaDev/Yii2User/*'])) {
                $app->get('i18n')->translations['AlexeiKaDev/Yii2User'] = [
                    'class' => PhpMessageSource::class,
                    'basePath' => __DIR__ . '/messages',
                    'sourceLanguage' => 'en-US'
                ];
            }

            Yii::$container->set(Mailer::class, $module->mailer);

            $module->debug = $this->ensureCorrectDebugSetting();
        }
    }

    /** Ensure the module is not in DEBUG mode on production environments */
    public function ensureCorrectDebugSetting(): bool
    {
        if (!defined('YII_DEBUG')) {
            return false;
        }

        if (!defined('YII_ENV')) {
            return false;
        }

        if (defined('YII_ENV') && YII_ENV !== 'dev') {
            return false;
        }

        if (defined('YII_DEBUG') && YII_DEBUG !== true) {
            return false;
        }

        $userModule = Yii::$app->getModule('user');

        if ($userModule instanceof Module) {
            return $userModule->debug;
        }

        return false;
    }
}
