# Docker Development Environment

Quick start guide for developing yii2-user with Docker.

## 🚀 Quick Start

### 1. Start All Services

```bash
docker-compose up -d
```

### 2. Install Dependencies

```bash
docker-compose exec php composer install
```

### 3. Run Migrations

```bash
# MySQL
docker-compose exec php php yii migrate --migrationPath=@vendor/alexeikadev/yii2-user/migrations

# PostgreSQL
docker-compose exec php php yii migrate --migrationPath=@vendor/alexeikadev/yii2-user/migrations --db=pgsql
```

### 4. Access Services

| Service | URL | Credentials |
|---------|-----|-------------|
| Application | http://localhost:8080 | - |
| phpMyAdmin | http://localhost:8081 | root / root |
| Adminer | http://localhost:8082 | yii2 / yii2 |
| MailHog | http://localhost:8025 | - |

## 📦 Services Overview

### PHP Application
- **Image**: yiisoftware/yii2-php:8.2-apache
- **Port**: 8080
- **Purpose**: Run your Yii2 application

### MySQL
- **Image**: mysql:8.0
- **Port**: 3306
- **Database**: yii2_user_test
- **Credentials**: yii2 / yii2

### PostgreSQL
- **Image**: postgres:15-alpine
- **Port**: 5432
- **Database**: yii2_user_test
- **Credentials**: yii2 / yii2

### phpMyAdmin
- **Image**: phpmyadmin/phpmyadmin
- **Port**: 8081
- **Purpose**: MySQL database management

### Adminer
- **Image**: adminer
- **Port**: 8082
- **Purpose**: Universal database management (MySQL + PostgreSQL)

### MailHog
- **Image**: mailhog/mailhog
- **SMTP Port**: 1025
- **Web UI**: 8025
- **Purpose**: Email testing (catches all outgoing emails)

## 🔧 Common Commands

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f php
docker-compose logs -f mysql
```

### Execute Commands

```bash
# PHP commands
docker-compose exec php php -v
docker-compose exec php composer --version

# Yii commands
docker-compose exec php php yii help

# Run tests
docker-compose exec php vendor/bin/codecept run
```

### Database Access

```bash
# MySQL
docker-compose exec mysql mysql -u yii2 -pyii2 yii2_user_test

# PostgreSQL
docker-compose exec postgres psql -U yii2 yii2_user_test
```

### Stop Services

```bash
# Stop all
docker-compose stop

# Stop specific service
docker-compose stop php
```

### Remove Everything

```bash
# Stop and remove containers
docker-compose down

# Remove containers and volumes
docker-compose down -v
```

## ⚙️ Configuration

### Database Connection

#### MySQL
```php
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=mysql;dbname=yii2_user_test',
    'username' => 'yii2',
    'password' => 'yii2',
    'charset' => 'utf8mb4',
],
```

#### PostgreSQL
```php
'pgsql' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=postgres;dbname=yii2_user_test',
    'username' => 'yii2',
    'password' => 'yii2',
    'charset' => 'utf8',
],
```

### Mailer (MailHog)

```php
'mailer' => [
    'class' => 'yii\symfonymailer\Mailer',
    'transport' => [
        'scheme' => 'smtp',
        'host' => 'mailhog',
        'port' => 1025,
    ],
],
```

## 🔍 Troubleshooting

### Port Already in Use

If port 8080 (or others) is already in use, edit `docker-compose.yml`:

```yaml
php:
  ports:
    - "8081:80"  # Changed from 8080
```

### Permission Issues

```bash
# Fix file permissions
docker-compose exec php chmod -R 777 runtime web/assets
```

### Database Connection Failed

```bash
# Check if database is ready
docker-compose exec mysql mysqladmin ping -h localhost

# Restart database
docker-compose restart mysql
```

### Clear Cache

```bash
docker-compose exec php php yii cache/flush-all
```

## 🧪 Testing

### Run All Tests

```bash
docker-compose exec php vendor/bin/codecept run
```

### Run Specific Tests

```bash
# Unit tests only
docker-compose exec php vendor/bin/codecept run unit

# With coverage
docker-compose exec php vendor/bin/codecept run --coverage
```

## 🎯 Development Workflow

### 1. Start Development

```bash
docker-compose up -d
docker-compose logs -f php
```

### 2. Make Changes

Edit files in your local directory. Changes are automatically reflected (volume mounted).

### 3. Test Changes

```bash
docker-compose exec php vendor/bin/codecept run
```

### 4. Check Emails

Visit http://localhost:8025 to see all emails sent by your application.

### 5. Stop Development

```bash
docker-compose stop
```

## 📊 Database Management

### Backup Database

```bash
# MySQL
docker-compose exec mysql mysqldump -u yii2 -pyii2 yii2_user_test > backup.sql

# PostgreSQL
docker-compose exec postgres pg_dump -U yii2 yii2_user_test > backup.sql
```

### Restore Database

```bash
# MySQL
docker-compose exec -T mysql mysql -u yii2 -pyii2 yii2_user_test < backup.sql

# PostgreSQL
docker-compose exec -T postgres psql -U yii2 yii2_user_test < backup.sql
```

### Reset Database

```bash
docker-compose down -v
docker-compose up -d
docker-compose exec php php yii migrate --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

## 🌐 Production Considerations

**⚠️ This Docker setup is for DEVELOPMENT ONLY!**

For production:
1. Use environment variables for secrets
2. Configure proper SSL/TLS
3. Use production-grade database
4. Set up proper backups
5. Configure monitoring
6. Use Docker secrets or Kubernetes secrets
7. Disable debug mode
8. Configure proper logging

## 📝 Custom Configuration

### Add More Services

Edit `docker-compose.yml`:

```yaml
redis:
  image: redis:alpine
  ports:
    - "6379:6379"
```

### Change PHP Version

```yaml
php:
  image: yiisoftware/yii2-php:8.4-apache  # Change version
```

### Add PHP Extensions

Create a `Dockerfile`:

```dockerfile
FROM yiisoftware/yii2-php:8.2-apache
RUN docker-php-ext-install gmp
```

Update `docker-compose.yml`:

```yaml
php:
  build: .
  # ... rest of config
```

## 💡 Tips

1. **Use MailHog** to test email workflows without sending real emails
2. **Check logs** regularly: `docker-compose logs -f`
3. **Use Adminer** to manage both MySQL and PostgreSQL from one interface
4. **Backup data** before running `docker-compose down -v`
5. **Monitor resource usage**: `docker stats`

## 🆘 Need Help?

- Docker Documentation: https://docs.docker.com/
- Yii2 Docker: https://github.com/yiisoft/yii2-docker
- GitHub Issues: https://github.com/AlexeiKaDev/yii2-user/issues

---

**Happy coding! 🚀**
