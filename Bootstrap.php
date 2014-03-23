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

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\web\PrefixUrlRule;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap(Application $app)
    {
        if ($app->hasModule('user')) {
            $identityClass = $app->getModule('user')->manager->userClass;
        } else {
            $app->setModule('user', [
                'class' => 'dektrium\user\Module'
            ]);
            $identityClass = 'dektrium\user\models\User';
        }

        $app->set('user', [
            'class' => 'yii\web\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/auth/login'],
            'identityClass' => $identityClass
        ]);

        $app->get('urlManager')->rules[] = new PrefixUrlRule([
            'prefix' => 'user',
            'rules' => [
                '<id:\d+>' => 'profile/show',
                '<action:(login|logout)>' => 'auth/<action>',
                '<action:(register|resend)>' => 'registration/<action>',
                'confirm/<id:\d+>/<token:\w+>' => 'registration/confirm',
                'forgot' => 'recovery/request',
                'recover/<id:\d+>/<token:\w+>' => 'recovery/reset',
                'settings/<action:\w+>' => 'settings/<action>'
            ]
        ]);
    }
}