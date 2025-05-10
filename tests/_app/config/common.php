<?php

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', __DIR__ . '/../../../../../');

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require_once(YII_APP_BASE_PATH . 'vendor/autoload.php'); 
require_once(YII_APP_BASE_PATH . 'vendor/yiisoft/yii2/Yii.php');

return [
    'id' => 'yii2-user-app',
    'basePath' => __DIR__ . '/..', // This would be _app directory
    // Используем __DIR__ для vendorPath и алиасов, если YII_APP_BASE_PATH не определен или закомментирован
    'vendorPath' => YII_APP_BASE_PATH . 'vendor', 
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@AlexeiKaDev/Yii2User' => YII_APP_BASE_PATH . 'AlexeiKaDev/yii2-user',
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            'viewPath' => '@AlexeiKaDev/Yii2User/mail',
            'useFileTransport' => true, // Essential for tests to not send actual emails
        ],
        'i18n' => [
            'translations' => [
                'user*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@AlexeiKaDev/Yii2User/messages',
                ],
            ],
        ],
    ],
    // Minimal other components needed for the module to function if any.
]; 