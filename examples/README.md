# Configuration Examples

This folder contains ready-to-use configuration examples for different scenarios.

## 📁 Available Examples

### 1. `basic-configuration.php`

Minimal configuration for getting started quickly.

**Features:**
- User registration
- Email confirmation
- Password recovery
- Basic security settings

**Use when:**
- Starting a new project
- Need simple user management
- Prototyping

**Quick start:**
```php
// Copy to your config/web.php or config/main.php
return array_merge_recursive(
    require __DIR__ . '/vendor/alexeikadev/yii2-user/examples/basic-configuration.php',
    [
        // Your other configurations
    ]
);
```

### 2. `advanced-security-configuration.php`

Complete security setup with all 2025 features.

**Features:**
- 2FA support
- Rate limiting
- Activity logging (GDPR compliant)
- HaveIBeenPwned integration
- Session management
- Security notifications
- OWASP 2025 & NIST 800-63B compliant

**Use when:**
- Production environments
- High security requirements
- Financial/healthcare applications
- Need compliance (GDPR, OWASP, NIST)

**Quick start:**
```php
// Merge with your existing config
return array_merge_recursive(
    require __DIR__ . '/vendor/alexeikadev/yii2-user/examples/advanced-security-configuration.php',
    [
        // Your customizations
        'params' => [
            'supportEmail' => 'your-support@example.com',
        ],
    ]
);
```

## 🚀 Usage Tips

### Customization

1. **Copy the example** to your config folder
2. **Modify settings** to match your requirements
3. **Test in development** before deploying to production

### Environment Variables

Use environment variables for sensitive data:

```php
'mailer' => [
    'transport' => [
        'username' => getenv('SMTP_USERNAME'),
        'password' => getenv('SMTP_PASSWORD'),
    ],
],
```

### Docker Development

Use the provided `docker-compose.yml` for local development:

```bash
docker-compose up -d
```

Then access:
- Application: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- Adminer: http://localhost:8082
- MailHog: http://localhost:8025

## 📋 Configuration Checklist

Before deploying to production:

- [ ] HTTPS is enabled
- [ ] Secure session cookies configured
- [ ] SMTP credentials are correct
- [ ] Email templates are customized
- [ ] Rate limiting is enabled
- [ ] Activity logging is enabled
- [ ] 2FA is configured (if needed)
- [ ] Security notifications are enabled
- [ ] Database backups are scheduled
- [ ] Error reporting is configured for production

## 🔒 Security Recommendations

1. **Always use HTTPS** in production
2. **Enable 2FA** for admin accounts
3. **Configure rate limiting** to prevent brute-force
4. **Enable activity logging** for audit trails
5. **Use environment variables** for secrets
6. **Regular security updates** (`composer update`)
7. **Monitor logs** for suspicious activity
8. **Test configurations** in staging first

## 📚 Additional Resources

- [Full Documentation](../README.md)
- [Security Policy](../SECURITY.md)
- [Upgrade Guide](../UPGRADE.md)
- [Russian Documentation](../README.ru.md)

## 💡 Need Help?

- GitHub Issues: https://github.com/AlexeiKaDev/yii2-user/issues
- Yii2 Forum: https://forum.yiiframework.com/
- Stack Overflow: Tag `yii2`

## 📝 Contributing

Found a better configuration? Submit a PR with your example!

1. Create a new file: `examples/your-example.php`
2. Add documentation in this README
3. Submit a pull request

---

**Remember**: Always review and test configurations before using in production!
