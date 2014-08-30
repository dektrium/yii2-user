<?php

return [
    'id' => 'yii2-test--console',
    'basePath' => dirname(__DIR__),
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => __DIR__ . '/../../../../migrations'
        ]
    ],
    'components' => [
        'log'   => null,
        'cache' => null,
        'db'    => require __DIR__ . '/db.php',
    ],
];