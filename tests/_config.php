<?php

return [
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'admins' => ['user']
        ]
    ],
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=dektrium_test',
        ],
        'mail' => [
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                // mailcatcher must be installed
                'host' => '127.0.0.1',
                'port' => '1025',
            ]
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];
