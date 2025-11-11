# Upgrade Guide

## Upgrading to PHP 7.2-8.4 Compatible Version

### Step 1: Update Composer

```bash
composer update alexeikadev/yii2-user
```

### Step 2: Run New Migrations (Optional but Recommended)

Run migrations for 2FA and rate limiting support:

```bash
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

This adds:
- `two_factor_enabled`, `two_factor_secret` columns (for 2FA)
- `allowance`, `allowance_updated_at` columns (for rate limiting)

### Step 3: ⚠️ CRITICAL - Regenerate Social Account Codes

Account codes changed from MD5 to SHA-256.

**Option A: Clear all social accounts**
```sql
TRUNCATE TABLE social_account;
```

**Option B: Migration**
```php
$this->update('{{%social_account}}', ['code' => null]);
```

### Step 4: ⚠️ WARNING - Password Length Increased

Minimum password length changed from 6 to 12 characters.

**Impact**: Existing users with passwords < 12 characters can still log in, but must update their password to meet the new requirement when changing it.

**Action Required**: None for existing users. New users must create passwords with at least 12 characters.

### Step 5: Clear Cache

```bash
./yii cache/flush-all
```

### Step 6: Test

- User registration
- User login  
- Social authentication
- Password recovery
- Admin functions

## Breaking Changes

1. **Social account codes** - Users must re-authenticate with social providers
2. **Password length** - Minimum increased from 6 to 12 characters
3. **Type hints removed** - Update custom extensions if they extend yii2-user classes
4. **Static $usernameRegexp** - Update if customized

## New Features (Optional)

### Two-Factor Authentication (2FA)

To enable 2FA support:

1. Install a 2FA module: `composer require hiqdev/yii2-mfa`
2. Implement `TwoFactorInterface` in your User model
3. See README.md for full implementation details

### Rate Limiting

To enable rate limiting protection:

1. Implement `RateLimitableInterface` in your User model
2. Configure `RateLimitFilter` in your controllers
3. See README.md for full implementation details

## Rollback

```bash
# Restore database backup
composer require dektrium/yii2-user
./yii cache/flush-all
```
