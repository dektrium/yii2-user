# Mailer

Yii2-user includes special component named Mailer, which is used to send emails in four different instances:

- Welcome message contains user's credentials, when `enableGeneratingPassword` is true.
- Registration confirmation message, when `enableConfirmation` is true.
- Email change confirmation message
- Recovery message

## Configuration

Mailer can be configured as followed:

```php
...
'user' => [
    'class' => 'dektrium\user\Module',
    'mailer' => [
        'sender'                => 'no-reply@myhost.com', // or ['no-reply@myhost.com' => 'Sender name']
        'welcomeSubject'        => 'Welcome subject',
        'confirmationSubject'   => 'Confirmation subject',
        'reconfirmationSubject' => 'Email change subject',
        'recoverySubject'       => 'Recovery subject',
],
...
```