<?php

return [
    'id'        => 'yii2-user-test',
    'basePath'  => dirname(__DIR__),
    'bootstrap' => [
        'dektrium\user\Bootstrap'
    ],
    'extensions' => require(VENDOR_DIR . '/yiisoft/extensions.php'),
    'aliases' => [
        '@dektrium/user' => realpath(__DIR__. '/../../../../'),
        '@vendor'        => VENDOR_DIR,
        '@bower'         => VENDOR_DIR . '/bower-asset',
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['user']
        ]
    ],
    'components' => [
        'assetManager' => [
            'basePath' => '@tests/codeception/app/assets'
        ],
        'log'   => null,
        'cache' => null,
        'request' => [
            'enableCsrfValidation'   => false,
            'enableCookieValidation' => false
        ],
        'db' => require __DIR__ . '/db.php',
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host'  => '127.0.0.1',
                'port'  => '1025',
            ]
        ],
    ],
];
