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

### 🔒 Security Improvements

#### Critical Security Fixes
- **FIXED**: Password generation now uses `random_int()` instead of `mt_rand()`
- **FIXED**: Username generation now uses Yii2 Security component
- **CHANGED**: Account codes now use SHA-256 instead of MD5
- **IMPROVED**: Password shuffling uses Fisher-Yates algorithm

### 🔧 Technical Changes (100 files affected)

- Removed 67 instances of `declare(strict_types=1)`
- Removed all return type declarations
- Removed all typed properties  
- Removed all typed parameters
- Converted Enum to class constants
- Replaced 9 arrow functions
- Replaced 2 match expressions

### 📊 Statistics
- **Files changed**: 100
- **Lines added**: +421
- **Lines removed**: -402
