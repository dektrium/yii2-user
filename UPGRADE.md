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

Minimum password length changed from 6 to **15 characters** (NIST 800-63B 2025 standard).

**Impact**: Existing users with passwords < 15 characters can still log in, but must update their password to meet the new requirement when changing it.

**Action Required**: None for existing users. New users must create passwords with at least 15 characters.

**Rationale**: NIST 800-63B (2025) requires 15 characters minimum for single-factor authentication, or 8 characters with MFA enabled.

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

1. **Social account codes** - Users must re-authenticate with social providers (MD5 → SHA-256)
2. **Password length** - Minimum increased from 6 to **15 characters** (NIST 800-63B 2025)
3. **Type hints removed** - Update custom extensions if they extend yii2-user classes
4. **Static $usernameRegexp** - Update if customized

## Security Standards Compliance

This update brings the package into full compliance with:
- **OWASP Top 10:2025** (RC1 - November 2025) - A07: Authentication Failures
- **NIST 800-63B (2025)** - Digital Identity Guidelines
- **PHP Security Best Practices 2025**

## New Features (Optional)

### Two-Factor Authentication (2FA)

Microsoft security data shows that enabling 2FA **reduces automated attacks by 99.9%**.

To enable 2FA support:

1. Install a 2FA module:
   - For PHP 7.2-8.0: `composer require hiqdev/yii2-mfa` or `composer require vxm/yii2-mfa`
   - For PHP 8.1+: `composer require simialbi/yii2-mfa` (updated April 2025)
2. Implement `TwoFactorInterface` in your User model
3. See README.md for full implementation details

### Rate Limiting

To enable rate limiting protection (OWASP Top 10:2025 recommended):

1. Implement `RateLimitableInterface` in your User model
2. Configure `RateLimitFilter` in your controllers
3. See README.md for full implementation details

### WebAuthn/Passkeys (Future-Ready)

For passwordless authentication (98% browser support in 2025):

1. Install: `composer require lbuchs/webauthn`
2. Implement FIDO2/WebAuthn authentication
3. See [lbuchs/WebAuthn](https://github.com/lbuchs/WebAuthn) for implementation details

## Rollback

```bash
# Restore database backup
composer require dektrium/yii2-user
./yii cache/flush-all
```
