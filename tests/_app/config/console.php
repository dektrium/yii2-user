<?php

return [
    'id' => 'yii2-user-tests-console',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@AlexeiKaDev/Yii2User' => dirname(dirname(dirname(__DIR__))),
        '@tests' => dirname(dirname(__DIR__)),
        '@vendor' => VENDOR_DIR,
    ],
    'components' => [
        'log' => null,
        'cache' => null,
        'db' => require __DIR__ . '/db.php',
    ],
];
