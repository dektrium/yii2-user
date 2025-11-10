# Yii2-user (AlexeiKaDev Fork)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-7.2%20to%208.4-blue.svg)](https://php.net)

Flexible user registration and authentication module for Yii2 with **full PHP 7.2-8.4 compatibility** and enhanced security.

## Features

- ✅ **PHP 7.2-8.4 Compatibility** - Works on all modern PHP versions
- 🔒 **Enhanced Security** - Cryptographically secure password generation, SHA-256 hashing
- 📧 Registration with optional email confirmation
- 🌐 Registration via social networks (Facebook, Google, GitHub, VKontakte, etc.)
- 🔑 Password recovery
- 👤 Account and profile management
- 💻 Console commands for user management
- 🛡️ User management interface with role-based access
- 👥 Ability to impersonate users (admin feature)

## Requirements

- PHP >= 7.2, < 8.5
- Yii2 >= 2.0.40
- One of the supported mailer extensions

## Installation

### Via Composer

```bash
composer require alexeikadev/yii2-user "dev-master"
```

### Run Migrations

```bash
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

### Configure

Add to your `config/web.php`:

```php
'modules' => [
    'user' => [
        'class' => 'AlexeiKaDev\Yii2User\Module',
        'enableConfirmation' => true,
        'enablePasswordRecovery' => true,
        'enableUnconfirmedLogin' => false,
        'confirmWithin' => 86400, // 24 hours
        'recoverWithin' => 21600,  // 6 hours
        'cost' => 12, // password hash cost
        'admins' => ['admin'], // admin usernames
    ],
],
'components' => [
    'user' => [
        'identityClass' => 'AlexeiKaDev\Yii2User\models\User',
        'enableAutoLogin' => true,
        'loginUrl' => ['/user/security/login'],
    ],
],
```

## Quick Start

### User Registration

```
http://yourapp.com/user/registration/register
```

### User Login

```
http://yourapp.com/user/security/login
```

### User Profile

```
http://yourapp.com/user/settings/profile
```

## Documentation

- [Full Documentation](docs/README.md)
- [Changelog](CHANGELOG.md)
- [Upgrade Guide](UPGRADE.md)

## What's New in This Fork

### PHP Compatibility
This fork has been completely rebuilt for **PHP 7.2-8.4 compatibility**:
- ✅ Removed all PHP 8+ specific syntax (enums, typed properties, union types)
- ✅ Compatible with PHP 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4
- ✅ 100 files refactored for maximum compatibility

### Security Enhancements
- 🔒 **Cryptographically secure password generation** using `random_int()`
- 🔒 **SHA-256 hashing** for account codes (replaces MD5)
- 🔒 **Secure username generation** via Yii Security component
- 🔒 **Fisher-Yates shuffle** algorithm for password randomization
- 🔒 Full CSRF, XSS, and SQL Injection protection

### Dependencies
- `php`: >=7.2 <8.5
- `yiisoft/yii2`: ^2.0.40
- `yiisoft/yii2-symfonymailer`: ^2.0.3|^3.0|^4.0
- `yiisoft/yii2-authclient`: ^2.1.0|^2.2|^3.0

## Upgrade from Previous Versions

⚠️ **Important**: When upgrading, social account codes need to be regenerated.

See the [Upgrade Guide](UPGRADE.md) for detailed instructions.

## Console Commands

### Create User

```bash
./yii user/create admin admin@example.com password
```

### Confirm User

```bash
./yii user/confirm admin
```

### Change Password

```bash
./yii user/password admin newpassword
```

### Delete User

```bash
./yii user/delete admin
```

## Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enableRegistration` | bool | `true` | Enable user registration |
| `enableConfirmation` | bool | `true` | Require email confirmation |
| `enablePasswordRecovery` | bool | `true` | Enable password recovery |
| `enableUnconfirmedLogin` | bool | `false` | Allow login without confirmation |
| `confirmWithin` | int | `86400` | Confirmation token lifetime (seconds) |
| `recoverWithin` | int | `21600` | Recovery token lifetime (seconds) |
| `cost` | int | `10` | Password hash cost (10-15 recommended) |
| `admins` | array | `[]` | Admin usernames |
| `adminPermission` | string | `null` | RBAC admin permission name |

## Social Authentication

Configure social providers in your `config/web.php`:

```php
'components' => [
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'google' => [
                'class' => 'AlexeiKaDev\Yii2User\clients\Google',
                'clientId' => 'your-client-id',
                'clientSecret' => 'your-client-secret',
            ],
            'facebook' => [
                'class' => 'AlexeiKaDev\Yii2User\clients\Facebook',
                'clientId' => 'your-app-id',
                'clientSecret' => 'your-app-secret',
            ],
            'github' => [
                'class' => 'AlexeiKaDev\Yii2User\clients\GitHub',
                'clientId' => 'your-client-id',
                'clientSecret' => 'your-client-secret',
            ],
        ],
    ],
],
```

## Testing

```bash
composer install
./vendor/bin/codecept run
```

## PHP Version Support

| PHP Version | Status | Tested |
|-------------|--------|--------|
| 7.2 | ✅ Supported | ✅ Yes |
| 7.3 | ✅ Supported | ✅ Yes |
| 7.4 | ✅ Supported | ✅ Yes |
| 8.0 | ✅ Supported | ✅ Yes |
| 8.1 | ✅ Supported | ✅ Yes |
| 8.2 | ✅ Supported | ✅ Yes |
| 8.3 | ✅ Supported | ✅ Yes |
| 8.4 | ✅ Supported | ✅ Yes |

## Security

If you discover any security related issues, please open an issue on GitHub.

## Contributing

Anyone and everyone is welcome to contribute. Please take a moment to
review the [guidelines for contributing](.github/CONTRIBUTING.md).

## Credits

- **Original Author**: [Dmitry Erofeev](https://github.com/dmeroff)
- **Fork Maintainer**: [AlexeiKaDev](https://github.com/AlexeiKaDev)
- **Original Project**: [dektrium/yii2-user](https://github.com/dektrium/yii2-user)

## License

Yii2-user is released under the MIT License. See the bundled [LICENSE.md](LICENSE.md) for details.

## Support

- 📖 [Documentation](docs/README.md)
- 🐛 [Issue Tracker](https://github.com/AlexeiKaDev/yii2-user/issues)
- 💬 [Discussions](https://github.com/AlexeiKaDev/yii2-user/discussions)

---

**Note**: This is a fork focused on PHP 7.2-8.4 compatibility and security enhancements. For the latest features, see the [Changelog](CHANGELOG.md).
