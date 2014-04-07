<?php return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../_app/config/web.php'),
    [
        'modules' => [
            'user' => [
                'confirmable' => false,
            ]
        ],
    ]
);
