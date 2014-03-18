<?php return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../../config/web.php'),
    require(__DIR__ . '/../_config.php'),
    [
        'modules' => [
            'user' => [
                'trackable' => false,
                'confirmable' => false,
            ]
        ],
    ]
);
