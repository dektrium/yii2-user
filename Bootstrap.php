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

use dektrium\user\domain\interfaces\AttachableInterface;
use dektrium\user\models\User;
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
    /**
     * @var array
     */
    protected $attachable = [
        'dektrium\user\domain\UserConfirmation',
    ];

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        /** @var Module $module */
        if ($app->hasModule('user') && ($module = $app->getModule('user')) instanceof Module) {
            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'dektrium\user\commands';
            } else {
                Yii::$container->set('yii\web\User', [
                    'enableAutoLogin' => true,
                    'loginUrl'        => ['/user/security/login'],
                    'identityClass'   => get_class(Yii::createObject(User::className())),
                ]);

                $configUrlRule = [
                    'prefix' => $module->urlPrefix,
                    'rules'  => $module->urlRules,
                ];

                if ($module->urlPrefix != 'user') {
                    $configUrlRule['routePrefix'] = 'user';
                }

                $configUrlRule['class'] = 'yii\web\GroupUrlRule';
                $rule = Yii::createObject($configUrlRule);
                
                $app->urlManager->addRules([$rule], false);

                if (!$app->has('authClientCollection')) {
                    $app->set('authClientCollection', [
                        'class' => Collection::className(),
                    ]);
                }
            }

            if (!isset($app->get('i18n')->translations['user*'])) {
                $app->get('i18n')->translations['user*'] = [
                    'class' => PhpMessageSource::className(),
                    'basePath' => __DIR__ . '/messages',
                    'sourceLanguage' => 'en-US'
                ];
            }

            Yii::$container->set('dektrium\user\Mailer', $module->mailer);

            foreach ($this->attachable as $name) {
                $this->createAttachableObject($name)->attachEventHandlers();
            }
        }
    }

    /**
     * @param  string $name
     * @return AttachableInterface
     */
    protected function createAttachableObject($name)
    {
        return new $name;
    }
}
