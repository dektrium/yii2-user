<?php

$db = require __DIR__ . '/db.php';

return [
    'id' => 'test-console',
    'basePath' => dirname(__DIR__),
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => __DIR__ . '/../../../migrations'
        ]
    ],
    'components' => [
        'log'   => null,
        'cache' => null,
        'db'    => $db,
    ],
];