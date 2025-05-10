<?php

return yii\helpers\ArrayHelper::merge(require(__DIR__ . '/common.php'), [
    'components' => [
        'request' => [
            'cookieValidationKey' => 'test',
            'scriptFile' => dirname(dirname(__DIR__)) . '/tests/_app/yii.php',
            'scriptUrl' => 'yii.php',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ]
    ],
    'bootstrap' => ['AlexeiKaDev\Yii2User\Bootstrap'],
    'modules' => [
        'user' => [
            'class' => 'AlexeiKaDev\Yii2User\Module',
            'admins' => ['user']
        ]
    ]
]);
