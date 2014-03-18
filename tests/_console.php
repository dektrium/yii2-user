<?php return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../config/console.php'),
    [
        'components' => [
            'db' => [
                'dsn' => 'mysql:host=localhost;dbname=dektrium_test',
            ]
        ]
    ]
);
