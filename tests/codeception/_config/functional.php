<?php

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME']     = YII_TEST_ENTRY_URL;

/**
 * Application configuration for functional tests
 */
return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../_app/config/web.php'),
    [
        'modules' => [
            'user' => [
                'mailer' => [
                    'class' => 'app\components\MailerMock',
                ],
            ]
        ],
    ]
);