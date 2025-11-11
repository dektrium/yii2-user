<?php
/**
 * Basic Yii2-User Configuration Example
 *
 * This is a minimal configuration example for yii2-user module.
 * Copy this to your main config file and customize as needed.
 */

return [
    'modules' => [
        'user' => [
            'class' => 'AlexeiKaDev\Yii2User\Module',

            // Enable email confirmation (recommended)
            'enableConfirmation' => true,

            // Allow users to recover their passwords
            'enablePasswordRecovery' => true,

            // Enable registration
            'enableRegistration' => true,

            // Require email confirmation before login
            'enableUnconfirmedLogin' => false,

            // Admin email for notifications
            'adminEmail' => 'admin@example.com',

            // Token expiration (24 hours)
            'confirmWithin' => 86400,
            'recoverWithin' => 86400,

            // Cost parameter for bcrypt (10-15 recommended)
            'cost' => 12,

            // URL rules (optional, for pretty URLs)
            'urlRules' => [
                '<id:\d+>'                  => 'profile/show',
                '<action:(login|logout)>'   => 'security/<action>',
                '<action:(register|resend)>' => 'registration/<action>',
                'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
                'forgot'                    => 'recovery/request',
                'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
                'settings/<action:\w+>'     => 'settings/<action>',
            ],
        ],
    ],

    // Component configuration
    'components' => [
        'user' => [
            'identityClass' => 'AlexeiKaDev\Yii2User\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity', 'httpOnly' => true],
            'loginUrl' => ['/user/security/login'],
        ],

        // Mailer configuration (required for email features)
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'viewPath' => '@AlexeiKaDev/Yii2User/views/mail',
            'useFileTransport' => false,
            'transport' => [
                'scheme' => 'smtp',
                'host' => 'smtp.example.com',
                'username' => 'noreply@example.com',
                'password' => 'your-password',
                'port' => 587,
                'encryption' => 'tls',
            ],
        ],
    ],
];
