# Configuration

All available configuration options are listed below with their default values.

---

#### enableFlashMessages (Type: `boolean`, Default value: `true`)

If this option is set to `true`, module will show flash messages using
integrated widget. Otherwise you will need to handle it using your own widget,
like provided in [yii advanced template](https://github.com/yiisoft/yii2-app-advanced/blob/master/frontend/widgets/Alert.php).
The keys for those messages are `success`, `info`, `danger`, `warning`.

---

#### enableRegistration (Type: `boolean`, Default value: `true`)

If this option is set to `false`, users will not be able to register an account.
Registration page will throw `HttpNotFoundException`. However confirmation will
continue working and you as an administrator will be able to create an account
for user from admin interface.

---

#### enableGeneratingPassword (Type: `boolean`, Default value: `false`)

If this option is set to `true`, password field on registration page will be
hidden and password for user will be generated automatically. Generated password
will be 8 characters long and will be sent to user via email.

---

#### enableConfirmation (Type: `boolean`, Default value: `true`)

If this option is set to `true`, module sends email that contains a confirmation
link that user must click to complete registration.

---

#### enableUnconfirmedLogin (Type: `boolean`, Default value: `false`)

If this option is to `true`, users will be able to log in even though they
didn't confirm his account.

---

#### enablePasswordRecovery (Type: `boolean`, Default value: `true`)

If this option is to `true`, users will be able to recovery their forgotten
passwords.

---

#### emailChangingStrategy (Type: `integer`, Default value: `\dektrium\user\Module::STRATEGY_DEFAULT`)

When user tries change his password, there are three ways how this change will
happen:

- `STRATEGY_DEFAULT` This is default strategy. Confirmation message will be sent
to new user's email and user must click confirmation link.
- `STRATEGY_INSECURE` Email will be changed without any confirmation.
- `STRATEGY_SECURE` Confirmation messages will be sent to both new and old
user's email addresses and user must click both confirmation links.

---

#### confirmWithin (Type: `integer`, Default value: `86400` (24 hours))

The time in seconds before a confirmation token becomes invalid. After expiring
this time user have to request new confirmation token on special page.

---

#### rememberFor (Type: `integer`, Default value: `1209600` (2 weeks))

The time in seconds you want the user will be remembered without asking for
credentials.

---

#### recoverWithin (Type: `integer`, Default value: `21600` (6 hours))

The time in seconds before a recovery token becomes invalid. After expiring this
time user have to request new recovery message.

---

#### admins (Type: `array`, Default value: `[]`)

Yii2-user has special admin pages where you can manager registered users or
create new user accounts. You need to specify username of users that will be
able to access those pages.

---

#### cost (Type: `integer`, Default value: `10`)

Cost parameter used by the Blowfish hash algorithm. The higher the value of cost,
the longer it takes to generate the hash and to verify a password against it.
Higher `cost` therefore slows down a brute-force attack. For best protection
against brute for attacks, set it to the highest value that is tolerable on
production servers. The time taken to compute the hash doubles for every
increment by one of `cost`.

---

#### urlPrefix (Type: `string`, Default value: `user`)

The prefix for user module URL. By changing this value you will be able to chage
url prefix used by module. For example if you set it to `auth`, then all urls
will loke like `auth/login`, `auth/admin`, `auth/register`, etc.

---

#### urlRules (Type: `array`, Default value: `[]`)

The rules to be used in URL management.

Configuration example
---------------------

The configuration should be applied in your main configuration file:


```php
...
'modules' => [
    ...
    'user' => [
        'class' => 'dektrium\user\Module',
        'enableUnconfirmedLogin' => true,
        'confirmWithin' => 21600,
        'cost' => 12,
        'admins' => ['admin']
    ],
    ...
],
...
```
