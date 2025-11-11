# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased] - PHP 7.2-8.4 Compatibility Rebuild

### 🚀 Major Changes

#### PHP Compatibility
- **BREAKING**: Changed minimum PHP version from 8.3 to **7.2**
- **ADDED**: Support for PHP 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, 8.4
- Removed all PHP 8+ specific syntax for backward compatibility

#### Dependencies Updated
- `yiisoft/yii2`: ^2.0.52 → **^2.0.40**
- `yiisoft/yii2-symfonymailer`: ^4.0.0 → **^2.0.3|^3.0|^4.0**
- `yiisoft/yii2-authclient`: ^2.1.0 → **^2.1.0|^2.2|^3.0**

### 🔒 Security Improvements (2024 Standards)

#### Critical Security Fixes
- **FIXED**: Password generation now uses `random_int()` instead of `mt_rand()`
- **FIXED**: Username generation now uses Yii2 Security component
- **CHANGED**: Account codes now use SHA-256 instead of MD5
- **IMPROVED**: Password shuffling uses Fisher-Yates algorithm
- **CHANGED**: Minimum password length increased from 6 to **12 characters** (2024 best practice)

#### New Security Features
- **ADDED**: Two-Factor Authentication (2FA) support via `TwoFactorInterface`
- **ADDED**: Rate limiting support via `RateLimitableInterface`
- **ADDED**: `RateLimitFilter` for brute-force protection
- **ADDED**: Migrations for 2FA and rate limiting database columns
- **ADDED**: Suggested packages for 2FA (hiqdev/yii2-mfa, vxm/yii2-mfa) and security (google/recaptcha)

### 🔧 Technical Changes (100 files affected)

- Removed 67 instances of `declare(strict_types=1)`
- Removed all return type declarations
- Removed all typed properties  
- Removed all typed parameters
- Converted Enum to class constants
- Replaced 9 arrow functions
- Replaced 2 match expressions

### 📊 Statistics
- **Files changed**: 106
- **New files created**: 6
  - `interfaces/TwoFactorInterface.php`
  - `interfaces/RateLimitableInterface.php`
  - `filters/RateLimitFilter.php`
  - `migrations/m251111_120000_add_two_factor_columns.php`
  - `migrations/m251111_120100_add_rate_limiting_columns.php`
- **Files modified**: 100+
- **Lines added**: +800+
- **Lines removed**: -402

### 📝 Breaking Changes
- **Password length**: Minimum password length changed from 6 to 12 characters
- **Social account codes**: Users must re-authenticate with social providers (MD5 → SHA-256)
- **Type hints**: Removed all return types and typed properties (PHP 7.2 compatibility)
