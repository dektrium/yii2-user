# Confirmation service

Confirmation service is used to handle process of confirmation of user accounts. It provides ability to confirm
users either by email or by admin. If you want you can enable both at the same time.

## Configuration

Here is an example of configuring confirmation service with all available options with their default values:

```php
\Yii::$container->set('dektrium\user\service\ConfirmationService', [
    // Whether service is enabled
    'isEnabled' => true,
    // Whether users can log in whenever they confirmed their email or not
    'isLoginWhileUnconfirmedEnabled' => false,
    // Whether users must click link sent to them in order to complete registration.
    'isEmailConfirmationEnabled' => true,
    // Whether users need to be approved by admins
    'isAdminApprovalEnabled' => false,
    // Whether users should be automatically logged in after confirmation
    'isAutoLoginEnabled' => true,
]);
```

## Emails

This service injects confirmation link into registration email (`dektrium/user/views/mail/registration.php`) and it also
sends message about account approval (`dektrium/user/views/mail/approval.php`).

## Overriding

You may override bundled confirmation service by extending bundled one. In this case you will need to override it using
DI container:

```php
\Yii::$container->set('dektrium\user\service\ConfirmationService', 'app\service\CustomConfirmationService');
```