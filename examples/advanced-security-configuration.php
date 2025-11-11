<?php
/**
 * Advanced Security Configuration Example (2025 Standards)
 *
 * This configuration includes all advanced security features:
 * - 2FA support
 * - Rate limiting
 * - Activity logging
 * - HaveIBeenPwned integration
 * - Session management
 * - Security notifications
 *
 * OWASP Top 10:2025 & NIST 800-63B 2025 compliant.
 */

return [
    'modules' => [
        'user' => [
            'class' => 'AlexeiKaDev\Yii2User\Module',

            // Basic settings
            'enableConfirmation' => true,
            'enablePasswordRecovery' => true,
            'enableRegistration' => true,
            'enableUnconfirmedLogin' => false,
            'adminEmail' => 'security@example.com',

            // Security: NIST 800-63B 2025 (15-char minimum)
            'cost' => 13,  // Higher cost for better security

            // Token expiration
            'confirmWithin' => 86400,   // 24 hours
            'recoverWithin' => 21600,   // 6 hours (stricter)

            // Model map (if you extend models)
            'modelMap' => [
                'User' => 'app\models\User',
                'Profile' => 'app\models\Profile',
            ],
        ],
    ],

    'components' => [
        // User component with secure session
        'user' => [
            'identityClass' => 'AlexeiKaDev\Yii2User\models\User',
            'enableAutoLogin' => true,
            'authTimeout' => 1800, // 30 minutes inactivity
            'absoluteAuthTimeout' => 14400, // 4 hours absolute
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'secure' => true,  // HTTPS only
                'sameSite' => 'Lax', // CSRF protection
            ],
            'loginUrl' => ['/user/security/login'],
        ],

        // Secure session configuration
        'session' => [
            'class' => 'yii\web\Session',
            'cookieParams' => [
                'httponly' => true,
                'secure' => true,
                'sameSite' => 'Lax',
            ],
            'timeout' => 1800, // 30 minutes
        ],

        // Mailer with TLS
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'viewPath' => '@AlexeiKaDev/Yii2User/views/mail',
            'useFileTransport' => false,
            'transport' => [
                'scheme' => 'smtps',  // Secure SMTP
                'host' => 'smtp.example.com',
                'username' => 'noreply@example.com',
                'password' => 'your-secure-password',
                'port' => 465,
                'encryption' => 'ssl',
            ],
        ],

        // Request component with CSRF validation
        'request' => [
            'enableCsrfValidation' => true,
            'cookieValidationKey' => 'your-secret-key-here',
            'csrfCookie' => [
                'httpOnly' => true,
                'secure' => true,
                'sameSite' => 'Lax',
            ],
        ],

        // Security component
        'security' => [
            'passwordHashStrategy' => 'password_hash', // Use PHP's password_hash
        ],
    ],

    // Controller configuration with rate limiting
    'controllerMap' => [
        'user/security' => [
            'class' => 'AlexeiKaDev\Yii2User\controllers\SecurityController',
            'as rateLimiter' => [
                'class' => 'AlexeiKaDev\Yii2User\filters\RateLimitFilter',
                'only' => ['login'],
                'maxRequests' => 5,      // 5 attempts
                'timeWindow' => 300,     // per 5 minutes
                'banDuration' => 900,    // ban for 15 minutes
            ],
        ],
        'user/registration' => [
            'class' => 'AlexeiKaDev\Yii2User\controllers\RegistrationController',
            'as rateLimiter' => [
                'class' => 'AlexeiKaDev\Yii2User\filters\RateLimitFilter',
                'only' => ['register'],
                'maxRequests' => 3,      // 3 attempts
                'timeWindow' => 3600,    // per hour
            ],
        ],
    ],

    // Application params
    'params' => [
        'supportEmail' => 'support@example.com',
        'adminEmail' => 'admin@example.com',

        // Security features
        'security' => [
            // Enable activity logging
            'enableActivityLog' => true,
            'activityLogRetentionMonths' => 12, // GDPR: 12 months

            // Enable HaveIBeenPwned checks
            'enablePasswordBreachCheck' => true,

            // Enable session tracking
            'enableSessionTracking' => true,
            'maxSessionsPerUser' => 5,

            // Enable security notifications
            'enableSecurityNotifications' => true,
            'notifyOnNewDeviceLogin' => true,
            'notifyOnPasswordChange' => true,
            'notifyOnFailedLogins' => true,

            // 2FA settings
            'enable2FA' => true,
            'force2FAForAdmins' => true,
            'backupCodesCount' => 10,

            // Password policy (NIST 800-63B 2025)
            'passwordMinLength' => 15,
            'passwordMaxLength' => 72,
            'checkPasswordBreaches' => true,
        ],
    ],

    // Bootstrap configuration
    'bootstrap' => [
        [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ],
        ],
    ],

    // Security headers (add in web.php)
    'as beforeRequest' => [
        'class' => 'yii\filters\HttpCache',
    ],
];

/**
 * Usage Examples:
 *
 * 1. Enable Activity Logging:
 *    ActivityLog::log($userId, ActivityLog::ACTION_LOGIN);
 *
 * 2. Check Password Breaches:
 *    HaveIBeenPwned::checkPassword($password);
 *
 * 3. Track User Sessions:
 *    UserSession::createOrUpdate($userId);
 *
 * 4. Send Security Notifications:
 *    SecurityNotification::notifyNewDeviceLogin($user);
 *
 * 5. Generate 2FA Backup Codes:
 *    $codes = BackupCode::generate($userId, 10);
 */
