# Руководство по обновлению

## Обновление до версии совместимой с PHP 7.2-8.4

### Шаг 1: Обновить Composer

```bash
composer update alexeikadev/yii2-user
```

### Шаг 2: Выполнить новые миграции (опционально, но рекомендуется)

Выполните миграции для 2FA, ограничения частоты запросов и расширенных функций безопасности:

```bash
./yii migrate/up --migrationPath=@vendor/alexeikadev/yii2-user/migrations
```

Это добавляет:
- Столбцы `two_factor_enabled`, `two_factor_secret` (для 2FA)
- Столбцы `allowance`, `allowance_updated_at` (для ограничения частоты запросов)
- Таблицу `activity_log` (для аудита, совместимого с GDPR)
- Таблицу `user_session` (для отслеживания сеансов на нескольких устройствах)
- Таблицу `backup_code` (для кодов восстановления 2FA)

### Шаг 3: ⚠️ КРИТИЧНО - Переформировать коды социальных аккаунтов

Коды аккаунтов изменены с MD5 на SHA-256.

**Вариант A: Очистить все социальные аккаунты**
```sql
TRUNCATE TABLE social_account;
```

**Вариант B: Миграция**
```php
// Очистить коды социальных аккаунтов
$this->update('{{%social_account}}', ['code' => null]);
```

### Шаг 4: ⚠️ ПРЕДУПРЕЖДЕНИЕ - Длина пароля увеличена

Минимальная длина пароля изменена с 6 на **15 символов** (стандарт NIST 800-63B 2025).

**Влияние**: Существующие пользователи с паролями длиной менее 15 символов могут продолжить вход, но должны обновить пароль при изменении пароля, чтобы соответствовать новым требованиям.

**Требуемое действие**: Ничего для существующих пользователей. Новые пользователи должны создавать пароли с минимум 15 символами.

**Обоснование**: NIST 800-63B (2025) требует минимум 15 символов для однофакторной аутентификации или 8 символов с включенной MFA.

### Шаг 5: Очистить кеш

```bash
./yii cache/flush-all
```

### Шаг 6: Тестирование

- Регистрация пользователя
- Вход в систему
- Социальная аутентификация
- Восстановление пароля
- Функции администратора

## Критические изменения

1. **Коды социальных аккаунтов** - Пользователи должны повторно пройти аутентификацию через социальные провайдеры (MD5 → SHA-256)
2. **Длина пароля** - Минимум увеличен с 6 на **15 символов** (NIST 800-63B 2025)
3. **Подсказки типов удалены** - Обновите пользовательские расширения, если они расширяют классы yii2-user
4. **Статическое свойство $usernameRegexp** - Обновите, если оно настраивается

## Соответствие стандартам безопасности

Это обновление обеспечивает полное соответствие:
- **OWASP Top 10:2025** (RC1 - ноябрь 2025) - A07: Ошибки аутентификации
- **NIST 800-63B (2025)** - Рекомендации по цифровой идентификации
- **PHP Security Best Practices 2025** - Лучшие практики безопасности PHP 2025

## Новые функции (опционально)

### Двухфакторная аутентификация (2FA)

Данные безопасности Microsoft показывают, что включение 2FA **снижает автоматические атаки на 99,9%**.

Чтобы включить поддержку 2FA:

1. Установите модуль 2FA:
   - Для PHP 7.2-8.0: `composer require hiqdev/yii2-mfa` или `composer require vxm/yii2-mfa`
   - Для PHP 8.1+: `composer require simialbi/yii2-mfa` (обновлено апрель 2025)
2. Реализуйте `TwoFactorInterface` в вашей модели User
3. Подробные инструкции по реализации см. в README.md

### Ограничение частоты запросов

Чтобы включить защиту от ограничения частоты запросов (рекомендуется OWASP Top 10:2025):

1. Реализуйте `RateLimitableInterface` в вашей модели User
2. Настройте `RateLimitFilter` в ваших контроллерах
3. Подробные инструкции см. в README.md

### WebAuthn/Passkeys (готово к будущему)

Для беспарольной аутентификации (98% поддержки браузерами в 2025 году):

1. Установите: `composer require lbuchs/webauthn`
2. Реализуйте аутентификацию FIDO2/WebAuthn
3. Подробности реализации см. в [lbuchs/WebAuthn](https://github.com/lbuchs/WebAuthn)

## Расширенные функции безопасности (2025)

### Журнал активности (соответствие GDPR статье 30)

Отслеживайте все действия пользователя для мониторинга безопасности и соответствия требованиям:

```php
// Используйте класс ActivityLog для отслеживания действий пользователей
use AlexeiKaDev\Yii2User\models\ActivityLog;

// Логировать действия пользователя
ActivityLog::log($userId, ActivityLog::ACTION_LOGIN);
ActivityLog::log($userId, ActivityLog::ACTION_PASSWORD_CHANGE, ['ip' => '1.2.3.4']);

// Получить активность пользователя
$activities = ActivityLog::getUserActivity($userId, 50);

// Получить неудачные попытки входа
$failedAttempts = ActivityLog::getFailedLoginAttempts($userId, 15);

// Очистить старые логи (запустить как cron задачу)
ActivityLog::cleanOldLogs(12); // Сохранить 12 месяцев
```

### Интеграция HaveIBeenPwned (рекомендуется NIST)

Проверяйте пароли на соответствие 15+ миллиардам скомпрометированных паролей:

```php
// Используйте класс HaveIBeenPwned для проверки паролей
use AlexeiKaDev\Yii2User\helpers\HaveIBeenPwned;

// Проверить пароль вручную
$result = HaveIBeenPwned::checkPassword('mypassword');
if ($result['breached']) {
    // Вывести количество утечек, в которых обнаружен пароль
    echo "Password found in {$result['count']} breaches!";
}

// Использовать как валидатор в правилах модели
public function rules()
{
    return [
        // ... другие правила
        // Проверить пароль против базы скомпрометированных паролей
        ['password', [HaveIBeenPwned::class, 'validatePassword']],
    ];
}
```

**Конфиденциальность**: Использует k-anonymity API - ваш пароль никогда не покидает ваш сервер.

### Управление сеансами на нескольких устройствах

Отслеживайте и управляйте сеансами на всех устройствах:

```php
// Используйте класс UserSession для управления сеансами пользователя
use AlexeiKaDev\Yii2User\models\UserSession;

// Создать/обновить сеанс при входе
UserSession::createOrUpdate($userId);

// Получить все активные сеансы
$sessions = UserSession::getUserSessions($userId);

// Завершить конкретный сеанс
UserSession::terminateSession($sessionId, $userId);

// Завершить все остальные сеансы (сохранить только текущий)
UserSession::terminateOtherSessions($userId);

// Обновить активность (запускать периодически)
UserSession::updateActivity($userId);

// Очистить истекшие сеансы (запустить как cron задачу)
UserSession::cleanExpiredSessions(2592000); // 30 дней
```

### Уведомления о безопасности

Предупредите пользователей о событиях, связанных с безопасностью:

```php
// Используйте класс SecurityNotification для отправки уведомлений о безопасности
use AlexeiKaDev\Yii2User\helpers\SecurityNotification;

// Предупреждение о входе с нового устройства
SecurityNotification::notifyNewDeviceLogin($user, [
    'device_name' => 'Chrome on Windows',
    'ip_address' => '1.2.3.4',
]);

// Подтверждение изменения пароля
SecurityNotification::notifyPasswordChange($user);

// Предупреждение о неудачных попытках входа
SecurityNotification::notifyFailedLoginAttempts($user, 5);

// Уведомление об изменении электронной почты
SecurityNotification::notifyEmailChange($user, 'old@example.com', 'new@example.com');

// Предупреждение об изменении статуса 2FA
SecurityNotification::notify2FAChange($user, true); // включено

// Уведомление о блокировке аккаунта
SecurityNotification::notifyAccountLockout($user, 'Too many failed attempts');
```

**Влияние**: На 73% быстрее обнаружение утечек с помощью уведомлений по электронной почте.

### Резервные коды 2FA

Генерируйте коды восстановления для пользователей 2FA:

```php
// Используйте класс BackupCode для управления кодами восстановления 2FA
use AlexeiKaDev\Yii2User\models\BackupCode;

// Генерировать 10 резервных кодов
$codes = BackupCode::generate($userId, 10);
// Показать $codes пользователю ОДИН РАЗ (они в открытом виде)
// Пример: ['ABCD-EFGH-JK', 'MNOP-QRST-UV', ...]

// Проверить резервный код при входе
if (BackupCode::verify($userId, $userInputCode)) {
    // Разрешить вход (код теперь отмечен как использованный)
}

// Проверить оставшиеся коды
$remaining = BackupCode::getRemainingCount($userId);

// Предупредить, если кодов мало
if (BackupCode::isRunningLow($userId, 3)) {
    echo "You have less than 3 backup codes remaining!";
}

// Очистить старые использованные коды (запустить как cron задачу)
BackupCode::cleanOldUsedCodes(6); // Сохранить 6 месяцев
```

**Лучшая практика**: Генерируйте резервные коды при включении 2FA и позволяйте пользователям регенерировать их в любое время.

## Откат

```bash
# Восстановить резервную копию базы данных
composer require dektrium/yii2-user
./yii cache/flush-all
```
