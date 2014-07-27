<?php

$db = require __DIR__ . '/db.php';

return [
    'id' => 'test',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'dektrium\user\Bootstrap'
    ],
    'extensions' => require(VENDOR_DIR . '/yiisoft/extensions.php'),
    'aliases' => [
        '@dektrium/user' => realpath(__DIR__. '/../../../'),
        '@vendor' => VENDOR_DIR
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['user']
        ]
    ],
    'components' => [
        'assetManager' => [
            'basePath' => '@tests/_app/assets'
        ],
        'log'   => null,
        'cache' => null,
        'request' => [
            'enableCsrfValidation'   => false,
            'enableCookieValidation' => false
        ],
        'db' => $db,
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => '127.0.0.1',
                'port' => '1025',
            ]
        ],
    ],
];
