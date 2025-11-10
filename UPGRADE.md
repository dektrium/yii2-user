# Upgrade Guide

## Upgrading to PHP 7.2-8.4 Compatible Version

### Step 1: Update Composer

```bash
composer update alexeikadev/yii2-user
```

### Step 2: ⚠️ CRITICAL - Regenerate Social Account Codes

Account codes changed from MD5 to SHA-256.

**Option A: Clear all social accounts**
```sql
TRUNCATE TABLE social_account;
```

**Option B: Migration**
```php
$this->update('{{%social_account}}', ['code' => null]);
```

### Step 3: Clear Cache

```bash
./yii cache/flush-all
```

### Step 4: Test

- User registration
- User login  
- Social authentication
- Password recovery
- Admin functions

## Breaking Changes

1. **Social account codes** - Users must re-authenticate
2. **Type hints removed** - Update custom extensions
3. **Static $usernameRegexp** - Update if customized

## Rollback

```bash
# Restore database backup
composer require dektrium/yii2-user
./yii cache/flush-all
```
