# 🔐 Yii2 User Management Module

<div align="center">

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-7.2%20to%208.4-blue.svg)](https://php.net)
[![Yii2 Version](https://img.shields.io/badge/yii2-%5E2.0.40-green.svg)](https://www.yiiframework.com/)
[![OWASP 2025](https://img.shields.io/badge/OWASP-2025%20Compliant-success.svg)](https://owasp.org/Top10/)
[![NIST 800-63B](https://img.shields.io/badge/NIST-800--63B%202025-blue.svg)](https://pages.nist.gov/800-63-4/sp800-63b.html)

**Flexible, secure, and modern user management module for Yii2**

*Complete user authentication and authorization system with enterprise-grade security features*

[Features](#-key-features) •
[Installation](#-installation) •
[Documentation](#-documentation) •
[Security](#-security-features) •
[Examples](#-usage-examples)

</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Key Features](#-key-features)
- [Security Features](#-security-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
  - [Step 1: Install via Composer](#step-1-install-via-composer)
  - [Step 2: Run Migrations](#step-2-run-migrations)
  - [Step 3: Configure Application](#step-3-configure-application)
  - [Step 4: Update URLs (Optional)](#step-4-update-urls-optional)
- [Configuration](#-configuration)
  - [Module Configuration](#module-configuration)
  - [Advanced Configuration](#advanced-configuration)
  - [Mailer Configuration](#mailer-configuration)
- [Features Documentation](#-features-documentation)
  - [User Registration](#1-user-registration)
  - [Email Confirmation](#2-email-confirmation)
  - [Password Recovery](#3-password-recovery)
  - [Social Authentication](#4-social-authentication)
  - [Two-Factor Authentication (2FA)](#5-two-factor-authentication-2fa)
  - [Rate Limiting](#6-rate-limiting-brute-force-protection)
  - [User Profile Management](#7-user-profile-management)
  - [Admin Panel](#8-admin-panel)
  - [Console Commands](#9-console-commands)
  - [RBAC Integration](#10-rbac-integration)
  - [WebAuthn/Passkeys](#11-webauthnpasskeys-future-ready)
- [Usage Examples](#-usage-examples)
- [Available Routes](#-available-routes)
- [Database Schema](#-database-schema)
- [Customization](#-customization)
- [Events](#-events)
- [Troubleshooting](#-troubleshooting)
- [FAQ](#-frequently-asked-questions)
- [Migration Guide](#-migration-guide)
- [PHP Version Support](#-php-version-support)
- [Compliance & Standards](#-compliance--standards)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [Credits](#-credits)
- [License](#-license)

---

## 🎯 Overview

**Yii2 User** is a comprehensive, production-ready user management module for the Yii2 framework. This fork has been completely rebuilt to provide **PHP 7.2-8.4 compatibility** while implementing the latest security standards from OWASP Top 10:2025 and NIST 800-63B (2025).

### Why This Fork?

- ✅ **Maximum PHP Compatibility**: Works seamlessly with PHP 7.2 through 8.4
- 🔒 **2025 Security Standards**: OWASP Top 10:2025 and NIST 800-63B compliant
- 🚀 **Modern Features**: 2FA, Rate Limiting, WebAuthn ready
- 📦 **Production Ready**: Battle-tested in enterprise applications
- 🔧 **Highly Customizable**: Flexible architecture for custom requirements
- 📚 **Well Documented**: Comprehensive guides and examples

---

## ✨ Key Features

### 🔐 Authentication & Authorization

- **Multiple Registration Methods**
  - Standard email/password registration
  - Social network authentication (Facebook, Google, GitHub, VKontakte, etc.)
  - Admin-created accounts via console or web interface
  - Optional email confirmation

- **Secure Login System**
  - Bcrypt password hashing (configurable cost factor)
  - "Remember me" functionality
  - Account blocking/unblocking
  - Login history tracking
  - Session management

- **Password Management**
  - Secure password recovery via email
  - Password strength validation (15+ characters, NIST 800-63B 2025)
  - Password change with current password verification
  - Automatic password generation option

### 🛡️ Advanced Security Features

- **Two-Factor Authentication (2FA)**
  - TOTP (Time-based One-Time Password) support
  - QR code generation for authenticator apps
  - Recovery codes for account recovery
  - Microsoft data: 99.9% reduction in automated attacks

- **Rate Limiting & Brute-Force Protection**
  - Configurable login attempt limits
  - IP-based rate limiting for guests
  - User-based rate limiting for registered users
  - Automatic lockout after failed attempts
  - OWASP Top 10:2025 recommended

- **Modern Security Practices**
  - Cryptographically secure random generation (`random_int()`)
  - SHA-256 hashing for tokens and codes
  - Fisher-Yates shuffle for password randomization
  - CSRF protection on all forms
  - XSS prevention via output encoding
  - SQL injection protection via parameterized queries

### 👥 User Management

- **User Profiles**
  - Customizable profile fields
  - Avatar support (with custom implementations)
  - Timezone selection
  - Public profile pages
  - Profile visibility settings

- **Admin Features**
  - User CRUD operations via web interface
  - Bulk user operations
  - User impersonation for debugging
  - Account activation/deactivation
  - Role management (with RBAC integration)
  - User search and filtering

### 🌐 Social Authentication

Built-in support for popular OAuth providers:

- Google
- Facebook
- GitHub
- VKontakte (VK)
- Twitter
- LinkedIn
- Yandex

Easy to extend for additional providers!

### 💻 Developer-Friendly

- **Console Commands**
  - Create users from CLI
  - Confirm user accounts
  - Change passwords
  - Delete users
  - Batch operations

- **Extensibility**
  - Event system for custom logic
  - Customizable views and layouts
  - Override models and forms
  - Custom validators
  - Flexible routing

- **Multi-Language Support**
  - Built-in translations
  - Easy to add new languages
  - Message formatting

---

## 🔒 Security Features

This module implements security standards from **OWASP Top 10:2025** (RC1 - November 2025) and **NIST 800-63B (2025)**:

### Password Security

| Feature | Implementation | Standard |
|---------|---------------|----------|
| **Minimum Length** | 15 characters | NIST 800-63B 2025 |
| **Maximum Length** | 72 characters | Bcrypt limit |
| **Hashing Algorithm** | Bcrypt | Industry standard |
| **Cost Factor** | 10-15 (configurable) | OWASP recommended |
| **Randomization** | Fisher-Yates shuffle | Cryptographic quality |

### Authentication Security

| Feature | Protection Against | Evidence |
|---------|-------------------|----------|
| **2FA/TOTP** | Credential theft | 99.9% attack reduction (Microsoft) |
| **Rate Limiting** | Brute-force, credential stuffing | OWASP Top 10:2025 A07 |
| **SHA-256 Tokens** | Token collision, forgery | NIST approved |
| **Secure Random** | Predictable tokens | Uses `random_int()` |
| **Session Management** | Session hijacking | Secure cookie flags |

### Compliance

- ✅ **OWASP Top 10:2025** - A07: Authentication Failures
- ✅ **NIST 800-63B (2025)** - Digital Identity Guidelines
- ✅ **PHP Security Best Practices 2025**
- ✅ **GDPR Ready** - User data management and deletion
- ✅ **PCI DSS Friendly** - For payment applications

---

## 📦 Requirements

| Component | Version | Notes |
|-----------|---------|-------|
| **PHP** | 7.2 - 8.4 | Tested on all versions |
| **Yii2** | ^2.0.40 | Latest stable recommended |
| **Database** | MySQL 5.7+, PostgreSQL 9.6+, SQLite 3 | InnoDB for MySQL |
| **PHP Extensions** | mbstring, openssl, pdo | Usually pre-installed |
| **Mailer** | yii2-symfonymailer ^2.0.3\|^3.0\|^4.0 | For email functionality |

### Optional Requirements

| Feature | Package | Version |
|---------|---------|---------|
| **2FA (PHP 7.2-8.0)** | hiqdev/yii2-mfa | Latest |
| **2FA (PHP 8.1+)** | simialbi/yii2-mfa | ^1.0 (April 2025) |
| **Alternative 2FA** | vxm/yii2-mfa | Latest |
| **WebAuthn/Passkeys** | lbuchs/webauthn | ^3.0 |
| **reCAPTCHA** | google/recaptcha | ^1.2 |
| **HTTP Client** | yiisoft/yii2-httpclient | ^2.0 |

---

## 🚀 Installation

### Step 1: Install via Composer

```bash
composer require alexeikadev/yii2-user "dev-master"
```

### Step 2: Run Migrations

This will create the necessary database tables:

```bash
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

**Tables created:**
- `user` - Main user table
- `profile` - User profiles
- `social_account` - Social authentication accounts
- `token` - Password recovery and email confirmation tokens

**Optional migrations** (for advanced features):
```bash
# For 2FA support
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
# This includes m251111_120000_add_two_factor_columns.php
# Adds: two_factor_enabled, two_factor_secret

# For rate limiting
# Included in same migration: m251111_120100_add_rate_limiting_columns.php
# Adds: allowance, allowance_updated_at
```

### Step 3: Configure Application

Add to your `config/web.php`:

```php
return [
    'modules' => [
        'user' => [
            'class' => 'AlexeiKaDev\Yii2User\Module',
            // See configuration section for all options
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'AlexeiKaDev\Yii2User\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/security/login'],
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'secure' => true, // Set to true if using HTTPS
            ],
        ],
    ],
];
```

### Step 4: Update URLs (Optional)

Add to your `config/console.php` for console commands:

```php
return [
    'modules' => [
        'user' => [
            'class' => 'AlexeiKaDev\Yii2User\Module',
        ],
    ],
];
```

---

## ⚙️ Configuration

### Module Configuration

Here's a complete configuration example with all available options:

```php
'modules' => [
    'user' => [
        'class' => 'AlexeiKaDev\Yii2User\Module',

        // Enable/disable features
        'enableRegistration' => true,
        'enableConfirmation' => true,
        'enablePasswordRecovery' => true,
        'enableUnconfirmedLogin' => false,
        'enableGeneratingPassword' => false,
        'enableFlashMessages' => true,

        // Token lifetimes (in seconds)
        'confirmWithin' => 86400,  // 24 hours
        'recoverWithin' => 21600,  // 6 hours
        'rememberFor' => 1209600,  // 14 days

        // Password settings
        'cost' => 12, // Bcrypt cost factor (10-15)

        // Admin settings
        'admins' => ['admin', 'superadmin'], // Admin usernames
        'adminPermission' => null, // Or RBAC permission like 'admin'

        // Email change strategies
        // STRATEGY_INSECURE - no confirmation needed
        // STRATEGY_DEFAULT - confirmation on new email only
        // STRATEGY_SECURE - confirmation on both old and new emails
        'emailChangeStrategy' => \AlexeiKaDev\Yii2User\Module::STRATEGY_SECURE,

        // URL rules (if you want custom routes)
        'urlRules' => [
            '<id:\d+>' => 'profile/show',
            '<action:(login|logout)>' => 'security/<action>',
            '<action:(register|resend)>' => 'registration/<action>',
            'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
            'forgot' => 'recovery/request',
            'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
            'settings/<action:\w+>' => 'settings/<action>',
        ],

        // Model map for extending models
        'modelMap' => [
            'User' => 'app\models\User',
            'Profile' => 'app\models\Profile',
            // Add other models if you extend them
        ],

        // Controller map for custom controllers
        'controllerMap' => [
            // 'registration' => 'app\controllers\user\RegistrationController',
        ],
    ],
],
```

### Advanced Configuration

#### Mailer Configuration

```php
'components' => [
    'mailer' => [
        'class' => 'yii\symfonymailer\Mailer',
        'transport' => [
            'scheme' => 'smtp',
            'host' => 'smtp.gmail.com',
            'username' => 'your-email@gmail.com',
            'password' => 'your-app-password',
            'port' => 587,
            'encryption' => 'tls',
        ],
        'viewPath' => '@app/mail',
        'useFileTransport' => false,
    ],
],
```

#### Custom Mailer View

Override default email templates by creating your own views:

```php
// In module configuration
'modules' => [
    'user' => [
        // ...
        'mailer' => [
            'sender' => ['no-reply@example.com' => 'My Application'],
            'welcomeSubject' => 'Welcome to My App!',
            'confirmationSubject' => 'Confirm your email',
            'reconfirmationSubject' => 'Confirm your new email',
            'recoverySubject' => 'Reset your password',
        ],
    ],
],
```

---

## 📖 Features Documentation

### 1. User Registration

#### Basic Registration Flow

```php
// Users can register at:
http://yourapp.com/user/registration/register

// After registration:
// 1. User account is created (unconfirmed if enableConfirmation=true)
// 2. Confirmation email is sent
// 3. User must click link in email to confirm
// 4. After confirmation, user can login
```

#### Programmatic User Creation

```php
use AlexeiKaDev\Yii2User\models\User;

$user = new User();
$user->scenario = 'register';
$user->username = 'john_doe';
$user->email = 'john@example.com';
$user->password = 'securePassword123456'; // Min 15 chars

if ($user->register()) {
    // User created successfully
    // Confirmation email sent automatically if enabled
    echo "User created with ID: " . $user->id;
} else {
    // Handle errors
    print_r($user->errors);
}
```

#### Custom Registration Form

Extend the registration form to add custom fields:

```php
namespace app\models;

use AlexeiKaDev\Yii2User\models\RegistrationForm as BaseRegistrationForm;

class RegistrationForm extends BaseRegistrationForm
{
    public $phone;
    public $company;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['phone', 'string', 'max' => 20];
        $rules[] = ['company', 'string', 'max' => 100];
        return $rules;
    }

    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        // Create user using parent method
        $user = parent::register();

        if ($user) {
            // Save additional fields to profile
            $user->profile->phone = $this->phone;
            $user->profile->company = $this->company;
            $user->profile->save();
        }

        return $user;
    }
}
```

Then update model map in configuration:

```php
'modules' => [
    'user' => [
        'modelMap' => [
            'RegistrationForm' => 'app\models\RegistrationForm',
        ],
    ],
],
```

---

### 2. Email Confirmation

#### Configuration Options

```php
'modules' => [
    'user' => [
        'enableConfirmation' => true,
        'enableUnconfirmedLogin' => false, // Allow login before confirmation?
        'confirmWithin' => 86400, // Token valid for 24 hours
    ],
],
```

#### Manual Confirmation

```php
use AlexeiKaDev\Yii2User\models\User;

$user = User::findOne(['email' => 'user@example.com']);
if ($user && !$user->isConfirmed) {
    $user->confirm();
    echo "User confirmed!";
}
```

#### Resend Confirmation

Users can request a new confirmation email:

```php
// URL: /user/registration/resend
// Users enter their email address
// New confirmation token is generated and sent
```

---

### 3. Password Recovery

#### Recovery Flow

```php
// Step 1: User requests password recovery
http://yourapp.com/user/recovery/request

// Step 2: User receives email with recovery link
http://yourapp.com/user/recovery/reset?id=123&code=abc123

// Step 3: User enters new password
// Step 4: Password is updated, user can login
```

#### Programmatic Password Reset

```php
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\enums\TokenType;

$user = User::findOne(['email' => 'user@example.com']);

// Create recovery token
$token = Yii::createObject([
    'class' => Token::class,
    'user_id' => $user->id,
    'type' => TokenType::RECOVERY,
]);
$token->save();

// Send email manually or use mailer service
// ...

// Reset password with token
if ($token->isValid) {
    $user->password = 'newSecurePassword123456';
    $user->save();
    $token->delete();
}
```

#### Custom Password Validation

```php
namespace app\models;

use AlexeiKaDev\Yii2User\models\User as BaseUser;

class User extends BaseUser
{
    public function rules()
    {
        $rules = parent::rules();

        // Add custom password validation
        $rules[] = ['password', function($attribute) {
            $password = $this->$attribute;

            // Require at least one uppercase letter
            if (!preg_match('/[A-Z]/', $password)) {
                $this->addError($attribute, 'Password must contain at least one uppercase letter.');
            }

            // Require at least one number
            if (!preg_match('/[0-9]/', $password)) {
                $this->addError($attribute, 'Password must contain at least one number.');
            }

            // Check against common passwords
            $commonPasswords = ['password123456789', 'qwerty12345678'];
            if (in_array(strtolower($password), $commonPasswords)) {
                $this->addError($attribute, 'Password is too common.');
            }
        }];

        return $rules;
    }
}
```

---

### 4. Social Authentication

#### Setup OAuth Providers

```php
'components' => [
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'google' => [
                'class' => 'AlexeiKaDev\Yii2User\clients\Google',
                'clientId' => 'your-google-client-id',
                'clientSecret' => 'your-google-client-secret',
            ],
            'facebook' => [
                'class' => 'AlexeiKaDev\Yii2User\clients\Facebook',
                'clientId' => 'your-facebook-app-id',
                'clientSecret' => 'your-facebook-app-secret',
            ],
            'github' => [
                'class' => 'AlexeiKaDev\Yii2User\clients\GitHub',
                'clientId' => 'your-github-client-id',
                'clientSecret' => 'your-github-client-secret',
            ],
            'vkontakte' => [
                'class' => 'AlexeiKaDev\Yii2User\clients\VKontakte',
                'clientId' => 'your-vk-app-id',
                'clientSecret' => 'your-vk-app-secret',
            ],
        ],
    ],
],
```

#### Display Social Login Buttons

```php
// In your login view
<?= \AlexeiKaDev\Yii2User\widgets\Connect::widget([
    'baseAuthUrl' => ['/user/security/auth'],
]) ?>
```

#### Connect/Disconnect Social Accounts

```php
// Users can connect social accounts at:
http://yourapp.com/user/settings/networks

// Connect additional accounts while logged in
// Disconnect unwanted accounts
```

#### Custom OAuth Provider

```php
namespace app\clients;

use yii\authclient\OAuth2;

class CustomProvider extends OAuth2
{
    public $authUrl = 'https://provider.com/oauth/authorize';
    public $tokenUrl = 'https://provider.com/oauth/token';
    public $apiBaseUrl = 'https://api.provider.com';

    protected function initUserAttributes()
    {
        return $this->api('user', 'GET');
    }

    protected function defaultName()
    {
        return 'custom_provider';
    }

    protected function defaultTitle()
    {
        return 'Custom Provider';
    }
}
```

---

### 5. Two-Factor Authentication (2FA)

**Microsoft Security Data**: Enabling 2FA reduces automated attacks by **99.9%**.

#### Installation

```bash
# For PHP 7.2-8.0
composer require hiqdev/yii2-mfa

# For PHP 8.1+ (updated April 2025)
composer require simialbi/yii2-mfa

# Alternative
composer require vxm/yii2-mfa
```

#### Run Migration

```bash
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

This adds:
- `two_factor_enabled` (boolean)
- `two_factor_secret` (string)

#### Implement TwoFactorInterface

```php
namespace app\models;

use AlexeiKaDev\Yii2User\models\User as BaseUser;
use AlexeiKaDev\Yii2User\interfaces\TwoFactorInterface;

class User extends BaseUser implements TwoFactorInterface
{
    public function getIsTwoFactorEnabled()
    {
        return (bool)$this->two_factor_enabled;
    }

    public function getTwoFactorSecret()
    {
        return $this->two_factor_secret;
    }

    public function setTwoFactorSecret($secret)
    {
        $this->two_factor_secret = $secret;
        return $this->save(false, ['two_factor_secret']);
    }

    public function enableTwoFactor()
    {
        $this->two_factor_enabled = 1;
        return $this->save(false, ['two_factor_enabled']);
    }

    public function disableTwoFactor()
    {
        $this->two_factor_enabled = 0;
        $this->two_factor_secret = null;
        return $this->save(false, ['two_factor_enabled', 'two_factor_secret']);
    }
}
```

#### 2FA Setup Flow

```php
use hiqdev\yii2\mfa\GoogleAuthenticator;

// 1. Generate secret for user
$ga = new GoogleAuthenticator();
$secret = $ga->createSecret();

// 2. Save secret (encrypted recommended)
Yii::$app->user->identity->setTwoFactorSecret($secret);

// 3. Generate QR code for user to scan
$qrCodeUrl = $ga->getQRCodeGoogleUrl(
    'My App',
    $secret,
    'user@example.com'
);

// 4. Display QR code to user
echo '<img src="' . $qrCodeUrl . '" />';

// 5. User scans with authenticator app (Google Authenticator, Authy, etc.)
// 6. User enters code to verify
$code = $_POST['code'];
if ($ga->verifyCode($secret, $code)) {
    Yii::$app->user->identity->enableTwoFactor();
    echo "2FA enabled successfully!";
}
```

#### 2FA Login Flow

```php
// After username/password verification:
if (Yii::$app->user->identity->getIsTwoFactorEnabled()) {
    // Show 2FA code input
    $code = $_POST['two_factor_code'];

    $ga = new GoogleAuthenticator();
    $secret = Yii::$app->user->identity->getTwoFactorSecret();

    if ($ga->verifyCode($secret, $code, 2)) { // 2 = discrepancy tolerance
        // Code valid, complete login
        return $this->goHome();
    } else {
        // Code invalid
        Yii::$app->session->setFlash('error', 'Invalid 2FA code');
    }
}
```

---

### 6. Rate Limiting (Brute-Force Protection)

OWASP Top 10:2025 recommends rate limiting to prevent credential stuffing and brute-force attacks.

#### Run Migration

```bash
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

This adds:
- `allowance` (integer)
- `allowance_updated_at` (integer)

#### Implement RateLimitableInterface

```php
namespace app\models;

use AlexeiKaDev\Yii2User\models\User as BaseUser;
use AlexeiKaDev\Yii2User\interfaces\RateLimitableInterface;

class User extends BaseUser implements RateLimitableInterface
{
    /**
     * Returns rate limit: [requests, seconds]
     */
    public function getRateLimit($request, $action)
    {
        // 5 attempts per 60 seconds
        return [5, 60];

        // Or more complex logic:
        // return $this->isAdmin ? [100, 60] : [5, 60];
    }

    public function loadAllowance($request, $action)
    {
        return [
            $this->allowance,
            $this->allowance_updated_at
        ];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save(false, ['allowance', 'allowance_updated_at']);
    }
}
```

#### Configure Rate Limiter

```php
use AlexeiKaDev\Yii2User\filters\RateLimitFilter;

class SecurityController extends Controller
{
    public function behaviors()
    {
        return [
            'rateLimiter' => [
                'class' => RateLimitFilter::class,
                'only' => ['login'], // Apply only to login action
                'enableForGuests' => true, // Enable for unauthenticated users
                'maxRequests' => 5, // Max 5 requests
                'timeWindow' => 60, // Per 60 seconds
            ],
        ];
    }

    public function actionLogin()
    {
        // Your login logic
    }
}
```

#### Custom Rate Limiter

```php
'rateLimiter' => [
    'class' => RateLimitFilter::class,
    'only' => ['login'],
    'user' => function () {
        // Custom user identification
        $username = Yii::$app->request->post('LoginForm')['login'] ?? null;
        if ($username) {
            return User::findByUsername($username);
        }
        return null;
    },
    'tooManyRequestsHttpCode' => 429,
    'errorMessage' => 'Too many login attempts. Please try again in {n} seconds.',
],
```

---

### 7. User Profile Management

#### Default Profile Fields

- Username
- Email
- Name (first and last)
- Public email
- Website
- Location
- Bio/Description
- Timezone
- Gravatar email

#### Access User Profile

```php
// Get current user profile
$profile = Yii::$app->user->identity->profile;

echo $profile->name;
echo $profile->location;
echo $profile->bio;
```

#### Update Profile

```php
use AlexeiKaDev\Yii2User\models\Profile;

$profile = Yii::$app->user->identity->profile;
$profile->name = 'John Doe';
$profile->location = 'New York, USA';
$profile->bio = 'Software developer and open source enthusiast';
$profile->save();
```

#### Custom Profile Fields

Extend the Profile model:

```php
namespace app\models;

use AlexeiKaDev\Yii2User\models\Profile as BaseProfile;

class Profile extends BaseProfile
{
    // Add custom attributes in migration first
    // ALTER TABLE profile ADD COLUMN phone VARCHAR(20);
    // ALTER TABLE profile ADD COLUMN company VARCHAR(100);

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['phone', 'string', 'max' => 20];
        $rules[] = ['company', 'string', 'max' => 100];
        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['phone'] = 'Phone Number';
        $labels['company'] = 'Company';
        return $labels;
    }
}
```

Update model map:

```php
'modules' => [
    'user' => [
        'modelMap' => [
            'Profile' => 'app\models\Profile',
        ],
    ],
],
```

---

### 8. Admin Panel

#### Access Control

```php
// Configure admins by username
'modules' => [
    'user' => [
        'admins' => ['admin', 'superadmin'],
    ],
],

// Or use RBAC permission
'modules' => [
    'user' => [
        'adminPermission' => 'manageUsers',
    ],
],
```

#### Admin Routes

```
/user/admin/index          - User list
/user/admin/create         - Create new user
/user/admin/update?id=1    - Edit user
/user/admin/delete?id=1    - Delete user
/user/admin/confirm?id=1   - Confirm user
/user/admin/block?id=1     - Block user
/user/admin/switch?id=1    - Impersonate user
```

#### Programmatic User Management

```php
use AlexeiKaDev\Yii2User\models\User;

// Create user
$user = new User();
$user->scenario = 'create';
$user->username = 'newuser';
$user->email = 'newuser@example.com';
$user->password = 'securePassword123456';
$user->save();

// Confirm user
$user = User::findOne($userId);
$user->confirm();

// Block user
$user->block();

// Unblock user
$user->unblock();

// Delete user (soft delete if enabled)
$user->delete();
```

---

### 9. Console Commands

#### Available Commands

```bash
# Create user
./yii user/create <username> <email> <password>

# Example
./yii user/create admin admin@example.com SecurePass123456

# Confirm user
./yii user/confirm <username>

# Change password
./yii user/password <username> <new-password>

# Delete user
./yii user/delete <username>
```

#### Batch Operations

```php
#!/usr/bin/env php
<?php
// scripts/bulk-create-users.php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$application = new yii\console\Application([
    'id' => 'bulk-users',
    'basePath' => __DIR__ . '/..',
    'components' => [
        'db' => require(__DIR__ . '/../config/db.php'),
    ],
]);

use AlexeiKaDev\Yii2User\models\User;

$users = [
    ['user1', 'user1@example.com'],
    ['user2', 'user2@example.com'],
    ['user3', 'user3@example.com'],
];

foreach ($users as list($username, $email)) {
    $user = new User();
    $user->scenario = 'create';
    $user->username = $username;
    $user->email = $email;
    $user->password = 'TempPassword123456';

    if ($user->save()) {
        echo "Created: $username\n";
        $user->confirm(); // Auto-confirm
    } else {
        echo "Failed: $username - " . json_encode($user->errors) . "\n";
    }
}
```

---

### 10. RBAC Integration

#### Setup RBAC

```php
// Configure authManager
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
],
```

#### Create Permissions

```php
use yii\rbac\DbManager;

$auth = Yii::$app->authManager;

// Create permissions
$manageUsers = $auth->createPermission('manageUsers');
$manageUsers->description = 'Manage users';
$auth->add($manageUsers);

$viewUsers = $auth->createPermission('viewUsers');
$viewUsers->description = 'View users';
$auth->add($viewUsers);

// Create roles
$admin = $auth->createRole('admin');
$auth->add($admin);

$moderator = $auth->createRole('moderator');
$auth->add($moderator);

// Assign permissions to roles
$auth->addChild($admin, $manageUsers);
$auth->addChild($admin, $viewUsers);
$auth->addChild($moderator, $viewUsers);

// Assign role to user
$auth->assign($admin, $userId);
```

#### Check Permissions

```php
// In controllers
if (!Yii::$app->user->can('manageUsers')) {
    throw new ForbiddenHttpException('You are not allowed to perform this action.');
}

// In views
<?php if (Yii::$app->user->can('manageUsers')): ?>
    <a href="/user/admin/create">Create User</a>
<?php endif; ?>
```

---

### 11. WebAuthn/Passkeys (Future-Ready)

**WebAuthn** provides the most secure authentication method in 2025 with 98% browser support.

#### What are Passkeys?

- No passwords to remember or steal
- Biometric authentication (fingerprint, Face ID)
- Hardware security keys (YubiKey)
- Phishing-resistant
- FIDO2/WebAuthn standard

#### Installation

```bash
composer require lbuchs/webauthn
```

#### Basic Implementation

```php
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\Binary\ByteBuffer;

// Initialize WebAuthn
$webAuthn = new WebAuthn('My App', 'example.com', ['https://example.com']);

// Registration
$createArgs = $webAuthn->getCreateArgs(
    ByteBuffer::fromBase64Url($userId),
    $username,
    $userDisplayName,
    30, // timeout in seconds
    false, // requireResidentKey
    false, // requireUserVerification
);

// Send $createArgs to client JavaScript
// Client creates credential and sends back

// Validation
$clientDataJSON = $_POST['clientDataJSON'];
$attestationObject = $_POST['attestationObject'];
$challenge = $_SESSION['challenge'];

$data = $webAuthn->processCreate(
    $clientDataJSON,
    $attestationObject,
    $challenge,
    true, // requireUserVerification
    true, // requireUserPresence
);

// Store $data->credentialId and $data->credentialPublicKey for future authentication
```

**Note**: Full WebAuthn integration requires custom JavaScript and server-side implementation. See [lbuchs/WebAuthn](https://github.com/lbuchs/WebAuthn) for complete documentation.

---

## 💡 Usage Examples

### Example 1: Custom Registration with Terms Acceptance

```php
namespace app\models;

use AlexeiKaDev\Yii2User\models\RegistrationForm as BaseRegistrationForm;

class RegistrationForm extends BaseRegistrationForm
{
    public $terms_accepted;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['terms_accepted', 'required'];
        $rules[] = ['terms_accepted', 'compare', 'compareValue' => 1,
                    'message' => 'You must accept the terms and conditions'];
        return $rules;
    }
}
```

### Example 2: Email Whitelist

```php
namespace app\models;

use AlexeiKaDev\Yii2User\models\User as BaseUser;

class User extends BaseUser
{
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = ['email', function($attribute) {
            $allowedDomains = ['company.com', 'partner.com'];
            $email = $this->$attribute;
            $domain = substr(strrchr($email, "@"), 1);

            if (!in_array($domain, $allowedDomains)) {
                $this->addError($attribute, 'Only company email addresses are allowed.');
            }
        }];

        return $rules;
    }
}
```

### Example 3: Auto-Assign Role on Registration

```php
use AlexeiKaDev\Yii2User\events\UserEvent;
use AlexeiKaDev\Yii2User\models\User;

// In Bootstrap or config
Event::on(User::class, User::EVENT_AFTER_REGISTER, function ($event) {
    $user = $event->user;

    // Assign 'user' role automatically
    $auth = Yii::$app->authManager;
    $role = $auth->getRole('user');
    $auth->assign($role, $user->id);
});
```

### Example 4: Custom Profile Page

```php
// controllers/ProfileController.php
namespace app\controllers;

use yii\web\Controller;
use AlexeiKaDev\Yii2User\models\User;

class ProfileController extends Controller
{
    public function actionView($username)
    {
        $user = User::findOne(['username' => $username]);

        if (!$user || $user->isBlocked) {
            throw new NotFoundHttpException('User not found');
        }

        return $this->render('view', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }
}
```

### Example 5: Email Verification Before Sensitive Actions

```php
public function actionDeleteAccount()
{
    $user = Yii::$app->user->identity;

    // Send confirmation email
    $token = $this->createVerificationToken($user);
    $this->sendConfirmationEmail($user, $token);

    Yii::$app->session->setFlash('info',
        'We have sent a confirmation email. Please click the link to confirm account deletion.');

    return $this->redirect(['index']);
}

public function actionConfirmDelete($token)
{
    if ($this->verifyToken($token)) {
        $user = Yii::$app->user->identity;
        $user->delete();
        Yii::$app->user->logout();

        return $this->goHome();
    }

    throw new BadRequestHttpException('Invalid token');
}
```

---

## 🗺️ Available Routes

### Public Routes

| Route | Description |
|-------|-------------|
| `/user/registration/register` | User registration |
| `/user/registration/resend` | Resend confirmation email |
| `/user/registration/confirm` | Email confirmation |
| `/user/security/login` | User login |
| `/user/security/logout` | User logout |
| `/user/security/auth` | Social authentication |
| `/user/recovery/request` | Password recovery request |
| `/user/recovery/reset` | Password reset |

### Authenticated Routes

| Route | Description |
|-------|-------------|
| `/user/settings/profile` | Edit profile |
| `/user/settings/account` | Account settings |
| `/user/settings/networks` | Connected social accounts |
| `/user/settings/delete` | Delete account |
| `/user/profile/show?id=1` | View user profile |

### Admin Routes

| Route | Description |
|-------|-------------|
| `/user/admin/index` | User management |
| `/user/admin/create` | Create user |
| `/user/admin/update?id=1` | Edit user |
| `/user/admin/delete?id=1` | Delete user |
| `/user/admin/confirm?id=1` | Confirm user |
| `/user/admin/block?id=1` | Block user |
| `/user/admin/switch?id=1` | Impersonate user |

---

## 🗄️ Database Schema

### User Table

```sql
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(60) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `confirmed_at` int(11) DEFAULT NULL,
  `unconfirmed_email` varchar(255) DEFAULT NULL,
  `blocked_at` int(11) DEFAULT NULL,
  `registration_ip` varchar(45) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `flags` int(11) NOT NULL DEFAULT '0',
  `last_login_at` int(11) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `allowance` int(11) DEFAULT '0',
  `allowance_updated_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unique_username` (`username`),
  UNIQUE KEY `user_unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Profile Table

```sql
CREATE TABLE `profile` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `public_email` varchar(255) DEFAULT NULL,
  `gravatar_email` varchar(255) DEFAULT NULL,
  `gravatar_id` varchar(32) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `bio` text,
  `timezone` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_user_profile` FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Social Account Table

```sql
CREATE TABLE `social_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `provider` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `data` text,
  `code` varchar(32) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_unique` (`provider`,`client_id`),
  KEY `fk_user_account` (`user_id`),
  CONSTRAINT `fk_user_account` FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Token Table

```sql
CREATE TABLE `token` (
  `user_id` int(11) NOT NULL,
  `code` varchar(32) NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  UNIQUE KEY `token_unique` (`user_id`,`code`,`type`),
  CONSTRAINT `fk_user_token` FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

---

## 🎨 Customization

### Custom Views

Override any view by copying it to your application:

```
@app/views/user/registration/register.php  → Registration form
@app/views/user/security/login.php         → Login form
@app/views/user/settings/profile.php       → Profile settings
@app/views/user/admin/index.php            → Admin user list
```

### Custom Controllers

```php
namespace app\controllers\user;

use AlexeiKaDev\Yii2User\controllers\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function actionRegister()
    {
        // Custom logic before registration

        $result = parent::actionRegister();

        // Custom logic after registration

        return $result;
    }
}
```

Update controller map:

```php
'modules' => [
    'user' => [
        'controllerMap' => [
            'registration' => 'app\controllers\user\RegistrationController',
        ],
    ],
],
```

### Custom Models

Extend any model and update the model map:

```php
'modules' => [
    'user' => [
        'modelMap' => [
            'User' => 'app\models\User',
            'Profile' => 'app\models\Profile',
            'RegistrationForm' => 'app\models\RegistrationForm',
            'LoginForm' => 'app\models\LoginForm',
        ],
    ],
],
```

### Custom Email Templates

```php
// config/web.php
'modules' => [
    'user' => [
        'mailer' => [
            'viewPath' => '@app/mail/user',
            'sender' => ['noreply@example.com' => 'My Application'],
            'welcomeSubject' => 'Welcome to My App!',
            'confirmationSubject' => 'Please confirm your email',
            'reconfirmationSubject' => 'Confirm your new email address',
            'recoverySubject' => 'Reset your password',
        ],
    ],
],
```

Create custom templates:

```
@app/mail/user/welcome.php
@app/mail/user/confirmation.php
@app/mail/user/reconfirmation.php
@app/mail/user/recovery.php
```

---

## 🎬 Events

Listen to events for custom logic:

```php
use yii\base\Event;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\events\UserEvent;

// After registration
Event::on(User::class, User::EVENT_AFTER_REGISTER, function ($event) {
    /** @var User $user */
    $user = $event->user;

    // Send welcome email
    // Create user directory
    // Log registration
    // Assign default role
});

// After confirmation
Event::on(User::class, User::EVENT_AFTER_CONFIRM, function ($event) {
    $user = $event->user;
    // Grant access to features
});

// Before login
Event::on(User::class, User::EVENT_BEFORE_LOGIN, function ($event) {
    $user = $event->user;
    // Check if user is allowed to login
    // Log login attempt
});

// After login
Event::on(User::class, User::EVENT_AFTER_LOGIN, function ($event) {
    $user = $event->user;
    // Update last login timestamp
    // Log successful login
});

// After password change
Event::on(User::class, User::EVENT_AFTER_PASSWORD_CHANGE, function ($event) {
    $user = $event->user;
    // Send notification email
    // Invalidate other sessions
});
```

Available events:

- `EVENT_BEFORE_REGISTER`
- `EVENT_AFTER_REGISTER`
- `EVENT_BEFORE_CONFIRM`
- `EVENT_AFTER_CONFIRM`
- `EVENT_BEFORE_LOGIN`
- `EVENT_AFTER_LOGIN`
- `EVENT_BEFORE_LOGOUT`
- `EVENT_AFTER_LOGOUT`
- `EVENT_BEFORE_PASSWORD_CHANGE`
- `EVENT_AFTER_PASSWORD_CHANGE`
- `EVENT_BEFORE_ACCOUNT_DELETE`
- `EVENT_AFTER_ACCOUNT_DELETE`

---

## 🔧 Troubleshooting

### Common Issues

#### 1. "Class not found" errors

**Problem**: PHP can't find module classes.

**Solution**:
```bash
composer dump-autoload
php yii cache/flush-all
```

#### 2. Email not sending

**Problem**: Confirmation/recovery emails not arriving.

**Solutions**:
- Check mailer configuration in `config/web.php`
- Verify SMTP credentials
- Check spam folder
- Enable `useFileTransport` for testing:
  ```php
  'mailer' => [
      'useFileTransport' => true, // Saves emails to runtime/mail
  ],
  ```

#### 3. Social auth not working

**Problem**: OAuth redirect fails or shows errors.

**Solutions**:
- Verify client ID and secret
- Check redirect URI in OAuth provider settings
- Enable `authClientCollection` in config
- Ensure `authclient` extension is installed

#### 4. Password validation fails

**Problem**: Can't set password, validation errors.

**Solutions**:
- Check minimum length (15 characters for NIST 2025)
- Verify password doesn't contain username or email
- Check for custom validation rules
- Test with simple password first

#### 5. Rate limiting not working

**Problem**: Users not getting blocked after failed attempts.

**Solutions**:
- Verify migrations ran successfully
- Check `RateLimitableInterface` implementation
- Ensure `RateLimitFilter` is configured in controller
- Check cache component is working

#### 6. 2FA codes not validating

**Problem**: Valid TOTP codes rejected.

**Solutions**:
- Check server time is synchronized (NTP)
- Verify secret is stored correctly
- Increase discrepancy tolerance in `verifyCode()`
- Test with multiple time windows

#### 7. Database migration fails

**Problem**: Migration errors during installation.

**Solutions**:
```bash
# Check database connection
./yii migrate/create test_migration

# Run with trace
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations --interactive=0

# Check existing tables
SHOW TABLES LIKE 'user%';
```

#### 8. Permission denied errors

**Problem**: Access denied to admin features.

**Solutions**:
- Add username to `admins` array in config
- Or assign RBAC permission specified in `adminPermission`
- Check `Yii::$app->user->can('permission')` returns true
- Verify user is logged in

---

## ❓ Frequently Asked Questions

### General Questions

**Q: What's the difference between this fork and the original?**

A: This fork provides:
- Full PHP 7.2-8.4 compatibility (original requires PHP 8.3+)
- 2025 security standards (OWASP, NIST 800-63B)
- Built-in 2FA and rate limiting support
- WebAuthn/Passkey ready
- Updated dependencies
- Active maintenance

**Q: Can I upgrade from dektrium/yii2-user?**

A: Yes! See the [Migration Guide](#-migration-guide). Main changes:
- Social account codes use SHA-256 (users must re-auth)
- Password minimum is 15 characters
- Some PHP 8+ syntax removed

**Q: Is this production-ready?**

A: Yes! This module is:
- Used in production applications
- Follows Yii2 best practices
- OWASP 2025 compliant
- Thoroughly tested
- Actively maintained

### Security Questions

**Q: How secure is password storage?**

A: Very secure:
- Bcrypt hashing with configurable cost (10-15)
- Automatic salt generation
- Timing-attack resistant validation
- NIST 800-63B 2025 compliant

**Q: Should I enable 2FA?**

A: Yes! Benefits:
- 99.9% reduction in automated attacks (Microsoft data)
- Protection against password theft
- Industry standard for sensitive applications
- Easy to implement with provided interfaces

**Q: What's the minimum password length?**

A: 15 characters (NIST 800-63B 2025 standard).
- For single-factor authentication
- Can be 8 characters if 2FA is enabled
- Configurable in your custom User model

**Q: How does rate limiting work?**

A: Rate limiting prevents brute-force attacks:
- Tracks login attempts per user/IP
- Configurable limits (default: 5 per 60 seconds)
- Automatic lockout after exceeding limit
- OWASP Top 10:2025 recommended

### Technical Questions

**Q: Can I use this without email confirmation?**

A: Yes! Set in configuration:
```php
'enableConfirmation' => false,
```

**Q: How do I customize the login form?**

A: Extend the LoginForm model and update the view:
```php
'modelMap' => [
    'LoginForm' => 'app\models\LoginForm',
],
```

**Q: Can I add custom profile fields?**

A: Yes! Extend the Profile model:
1. Add columns to database
2. Extend Profile model
3. Update model map
4. Customize views

**Q: How do I integrate with existing user table?**

A: You'll need to:
1. Migrate existing data to match schema
2. Update password hashes to bcrypt
3. Add required columns
4. Or extend User model to work with your schema

**Q: Can I use PostgreSQL or SQLite?**

A: Yes! The module supports:
- MySQL 5.7+
- MariaDB 10.2+
- PostgreSQL 9.6+
- SQLite 3

**Q: How do I add a new OAuth provider?**

A: Extend `yii\authclient\OAuth2`:
```php
class CustomProvider extends \yii\authclient\OAuth2
{
    // Implement required methods
}
```

---

## 📚 Migration Guide

### From dektrium/yii2-user

#### Step 1: Update Composer

```bash
composer remove dektrium/yii2-user
composer require alexeikadev/yii2-user "dev-master"
```

#### Step 2: Run New Migrations

```bash
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

New migrations add:
- `two_factor_enabled`, `two_factor_secret` (for 2FA)
- `allowance`, `allowance_updated_at` (for rate limiting)

#### Step 3: Update Namespace

Change namespace in your config:

```php
// Old
'class' => 'dektrium\user\Module',
'identityClass' => 'dektrium\user\models\User',

// New
'class' => 'AlexeiKaDev\Yii2User\Module',
'identityClass' => 'AlexeiKaDev\Yii2User\models\User',
```

#### Step 4: Regenerate Social Account Codes

Social accounts changed from MD5 to SHA-256:

```sql
-- Option A: Clear all social accounts (users must re-authenticate)
TRUNCATE TABLE social_account;

-- Option B: Invalidate codes
UPDATE social_account SET code = NULL;
```

#### Step 5: Update Password Requirements

Minimum password changed from 6 to 15 characters:
- Existing users can still login
- Must update password on next change
- New registrations require 15+ characters

#### Step 6: Test Everything

```bash
# Clear cache
./yii cache/flush-all

# Test features
- User registration
- Email confirmation
- Login/logout
- Password recovery
- Social authentication (re-connect)
- Admin functions
```

---

## 🐘 PHP Version Support

| PHP Version | Status | Notes |
|-------------|--------|-------|
| 7.2 | ✅ Fully Supported | Tested |
| 7.3 | ✅ Fully Supported | Tested |
| 7.4 | ✅ Fully Supported | Tested |
| 8.0 | ✅ Fully Supported | Tested |
| 8.1 | ✅ Fully Supported | Tested |
| 8.2 | ✅ Fully Supported | Tested |
| 8.3 | ✅ Fully Supported | Tested |
| 8.4 | ✅ Fully Supported | Tested |

**Compatibility Notes**:
- No PHP 8+ specific syntax used
- No enums, typed properties, or union types
- Works with both PHP 7.2 legacy apps and PHP 8.4 modern apps
- Same codebase for all versions

---

## 📜 Compliance & Standards

### Security Standards

This package is compliant with:

- **OWASP Top 10:2025** (RC1 - November 2025)
  - A07: Identification and Authentication Failures
  - Implements all recommended controls

- **NIST 800-63B (2025)** - Digital Identity Guidelines
  - 15-character minimum passwords (single-factor)
  - 8-character minimum with MFA
  - No composition rules (uppercase, numbers, symbols)
  - No mandatory password expiration
  - Breach detection via blocklists

- **PHP Security Best Practices 2025**
  - Cryptographically secure random generation
  - Modern hashing algorithms
  - Protection against OWASP Top 10 vulnerabilities

### Privacy & Data Protection

- **GDPR Ready**
  - User data export
  - Right to deletion
  - Data processing consent
  - Privacy policy support

- **CCPA Compatible**
  - Data access controls
  - Opt-out mechanisms
  - Data deletion on request

---

## 🧪 Testing

### Run Tests

```bash
composer install
./vendor/bin/codecept run
```

### Run Specific Tests

```bash
# Unit tests
./vendor/bin/codecept run unit

# Functional tests
./vendor/bin/codecept run functional

# Acceptance tests
./vendor/bin/codecept run acceptance
```

### Code Coverage

```bash
./vendor/bin/codecept run --coverage --coverage-html
```

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### Ways to Contribute

- 🐛 Report bugs
- 💡 Suggest features
- 📖 Improve documentation
- 🔧 Submit pull requests
- ⭐ Star the repository

### Development Process

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Add tests for new features
- Update documentation
- Keep PHP 7.2 compatibility

### Reporting Issues

Please include:
- PHP version
- Yii2 version
- Module version
- Steps to reproduce
- Expected vs actual behavior
- Error messages or logs

---

## 👏 Credits

### Core Team

- **Original Author**: [Dmitry Erofeev](https://github.com/dmeroff)
- **Fork Maintainer**: [AlexeiKaDev](https://github.com/AlexeiKaDev)
- **Original Project**: [dektrium/yii2-user](https://github.com/dektrium/yii2-user)

### Contributors

Thanks to all contributors who helped improve this module!

### Inspiration

- Yii2 Framework Team
- OWASP Foundation
- NIST Cybersecurity Framework
- Security community

---

## 📄 License

This project is licensed under the **MIT License**.

```
MIT License

Copyright (c) 2025 AlexeiKaDev
Copyright (c) 2014-2023 Dmitry Erofeev

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

See [LICENSE.md](LICENSE.md) for full license text.

---

## 📞 Support & Resources

### Documentation

- 📖 [Full Documentation](docs/README.md)
- 📝 [Changelog](CHANGELOG.md)
- 🔄 [Upgrade Guide](UPGRADE.md)
- 💬 [API Documentation](docs/API.md)

### Community

- 🐛 [Issue Tracker](https://github.com/AlexeiKaDev/yii2-user/issues)
- 💬 [Discussions](https://github.com/AlexeiKaDev/yii2-user/discussions)
- 📧 [Email Support](mailto:alexei.ka.dev@example.com)

### Resources

- [Yii2 Framework](https://www.yiiframework.com/)
- [Yii2 Guide](https://www.yiiframework.com/doc/guide/2.0/en)
- [OWASP Top 10](https://owasp.org/Top10/)
- [NIST 800-63B](https://pages.nist.gov/800-63-4/sp800-63b.html)

---

<div align="center">

**⭐ If this project helps you, please give it a star! ⭐**

Made with ❤️ by [AlexeiKaDev](https://github.com/AlexeiKaDev)

[Report Bug](https://github.com/AlexeiKaDev/yii2-user/issues) ·
[Request Feature](https://github.com/AlexeiKaDev/yii2-user/issues) ·
[Documentation](docs/README.md)

</div>
