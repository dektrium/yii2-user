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

use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;
use yii\console\Application as ConsoleApplication;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{
    /** @inheritdoc */
    public function bootstrap($app)
    {
        /** @var $module Module */
        if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module){
            foreach ($module->modelMap as $name => $definition) {
                $class = "dektrium\\user\\models\\" . $name;
                \Yii::$container->set($class, $definition);
                if (is_array($definition)) {
                    $module->modelMap[$name] = $class;
                }
                if (in_array($name, ['User', 'Profile', 'Token', 'Account'])) {
                    \Yii::$container->set($name . 'Query', function () use ($definition) {
                        $class = is_array($definition) ? $definition['class'] : $definition;
                        return $class::find();
                    });
                }
            }
            \Yii::$container->setSingleton(Finder::className(), [
                'userQuery'    => \Yii::$container->get('UserQuery'),
                'profileQuery' => \Yii::$container->get('ProfileQuery'),
                'tokenQuery'   => \Yii::$container->get('TokenQuery'),
                'accountQuery' => \Yii::$container->get('AccountQuery'),
            ]);
            \Yii::$container->set('yii\web\User', [
                'enableAutoLogin' => true,
                'loginUrl'        => ['/user/security/login'],
                'identityClass'   => $module->modelMap['User'],
            ]);

            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'dektrium\user\commands';
            } else {
                $configUrlRule = [
                    'prefix' => $module->urlPrefix,
                    'rules'  => $module->urlRules
                ];

                if ($module->urlPrefix != 'user') {
                    $configUrlRule['routePrefix'] = 'user';
                }

                $app->get('urlManager')->rules[] = new GroupUrlRule($configUrlRule);

                if (!$app->has('authClientCollection')) {
                    $app->set('authClientCollection', [
                        'class' => 'yii\authclient\Collection',
                    ]);
                }
            }

            $app->get('i18n')->translations['user*'] = [
                'class'    => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__ . '/messages',
            ];

            $defaults = [
                'welcomeSubject'        => \Yii::t('user', 'Welcome to {0}', \Yii::$app->name),
                'confirmationSubject'   => \Yii::t('user', 'Confirm account on {0}', \Yii::$app->name),
                'reconfirmationSubject' => \Yii::t('user', 'Confirm email change on {0}', \Yii::$app->name),
                'recoverySubject'       => \Yii::t('user', 'Complete password reset on {0}', \Yii::$app->name)
            ];

            \Yii::$container->set('dektrium\user\Mailer', array_merge($defaults, $module->mailer));
        }
        
    }
}