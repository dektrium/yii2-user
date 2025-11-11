# Security Policy

## 🔒 Supported Versions

We actively support the following versions with security updates:

| Version | Supported          | PHP Support    | Security Standards |
| ------- | ------------------ | -------------- | ------------------ |
| 1.x     | :white_check_mark: | 7.2 - 8.4      | OWASP 2025, NIST 800-63B 2025 |
| < 1.0   | :x:                | Various        | Legacy             |

## 🚨 Reporting a Vulnerability

We take security vulnerabilities seriously. If you discover a security issue, please follow these guidelines:

### How to Report

**DO NOT** open a public GitHub issue for security vulnerabilities.

Instead, please report security vulnerabilities via:

1. **Email**: Send details to `alexei.ka.dev@example.com`
2. **Subject**: `[SECURITY] Brief description of vulnerability`
3. **Include**:
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (if any)
   - Your contact information

### What to Expect

- **Acknowledgment**: Within 48 hours
- **Initial Assessment**: Within 5 business days
- **Status Updates**: Every 7 days until resolved
- **Fix Timeline**: Critical issues within 30 days, others within 90 days

### Disclosure Policy

- We will coordinate disclosure timing with you
- We will credit you in the security advisory (unless you prefer to remain anonymous)
- We will publish a security advisory after the fix is released

## 🛡️ Security Features

This module implements industry-standard security practices:

### Authentication & Authorization

- **Bcrypt Password Hashing** (cost 10-15)
- **15-character minimum passwords** (NIST 800-63B 2025)
- **Two-Factor Authentication (2FA)** support
- **Rate Limiting** to prevent brute-force attacks
- **Session Management** across multiple devices
- **CSRF Protection** (Yii2 built-in)

### Data Protection

- **SQL Injection Prevention** (Yii2 Query Builder & prepared statements)
- **XSS Protection** (auto-escaping in views)
- **Password Breach Detection** (HaveIBeenPwned integration)
- **Secure Token Generation** (cryptographically secure `random_int()`)
- **SHA-256 hashing** for account codes (not MD5)

### Compliance

- ✅ **OWASP Top 10:2025** (RC1 - November 2025)
  - A01: Broken Access Control
  - A02: Cryptographic Failures
  - A03: Injection
  - A07: Authentication Failures
- ✅ **NIST 800-63B (2025)** - Digital Identity Guidelines
- ✅ **GDPR Article 30** - Activity logging and data retention
- ✅ **PHP Security Best Practices 2025**

### Advanced Security Features (2025)

- **Activity Log**: GDPR-compliant audit trails
- **HaveIBeenPwned**: Password breach detection (15+ billion passwords)
- **Security Notifications**: Alerts for suspicious activity
- **Session Tracking**: Multi-device session management
- **Backup Codes**: 2FA recovery codes

## 🔐 Security Best Practices for Users

### For Developers

1. **Keep Dependencies Updated**
   ```bash
   composer update
   ```

2. **Enable 2FA** for admin accounts
   ```php
   $user->enableTwoFactor();
   ```

3. **Configure Rate Limiting**
   ```php
   'controllerMap' => [
       'security' => [
           'as rateLimiter' => [
               'class' => RateLimitFilter::class,
           ],
       ],
   ],
   ```

4. **Use HTTPS** in production (required for secure cookies)

5. **Enable Activity Logging**
   ```php
   // In your SecurityController
   ActivityLog::log($userId, ActivityLog::ACTION_LOGIN);
   ```

6. **Configure Secure Mailer**
   ```php
   'components' => [
       'mailer' => [
           'transport' => [
               'scheme' => 'smtps', // Use TLS
               'encryption' => 'tls',
           ],
       ],
   ],
   ```

7. **Set Secure Session Configuration**
   ```php
   'components' => [
       'session' => [
           'cookieParams' => [
               'httponly' => true,
               'secure' => true,
               'sameSite' => 'Lax',
           ],
       ],
   ],
   ```

### For End Users

1. Use **strong, unique passwords** (minimum 15 characters)
2. Enable **Two-Factor Authentication** (2FA)
3. Don't reuse passwords across sites
4. Use a **password manager**
5. Review **active sessions** regularly
6. Enable **security notifications**

## 📋 Security Checklist

Before deploying to production, ensure:

- [ ] HTTPS is enabled
- [ ] Session cookies are secure (`httponly`, `secure`, `sameSite`)
- [ ] CSRF protection is enabled
- [ ] Rate limiting is configured
- [ ] Activity logging is enabled
- [ ] Email notifications are configured
- [ ] Database credentials are secure
- [ ] Debug mode is disabled (`YII_DEBUG = false`)
- [ ] Error reporting doesn't expose sensitive data
- [ ] File permissions are correct (no 777)
- [ ] Firewall rules are configured
- [ ] Backups are scheduled
- [ ] Security monitoring is in place

## 🔄 Security Update Process

1. **Monitor**: We monitor security advisories for Yii2 and dependencies
2. **Assess**: Evaluate impact on this module
3. **Fix**: Develop and test security patches
4. **Release**: Publish updates via GitHub and Packagist
5. **Notify**: Announce via GitHub Security Advisories

## 📚 Security Resources

- [OWASP Top 10](https://owasp.org/Top10/)
- [NIST 800-63B](https://pages.nist.gov/800-63-4/sp800-63b.html)
- [Yii2 Security Best Practices](https://www.yiiframework.com/doc/guide/2.0/en/security-best-practices)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [HaveIBeenPwned](https://haveibeenpwned.com/)

## 🏆 Security Credits

We would like to thank the following individuals for responsibly disclosing security vulnerabilities:

- *(No vulnerabilities reported yet)*

## 📞 Contact

For security-related questions (non-vulnerabilities):
- GitHub Issues: https://github.com/AlexeiKaDev/yii2-user/issues
- Email: alexei.ka.dev@example.com

---

**Remember**: Security is a shared responsibility. Stay vigilant and keep your systems updated!
