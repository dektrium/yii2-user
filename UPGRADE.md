# Upgrading instructions for Yii2-user

The following upgrading instructions are cumulative. That is, if you want to
upgrade from version A to version C and there is version B between A and C, you
need to following the instructions for both A and B.

## Upgrade from Yii2-user 0.9.* to Yii2-user 1.0.0-alpha1

- **APPLY NEW MIGRATIONS!**

- Admin view files have been updated. If you were overriding admin views, then change them accordingly to use new
 functionality.

- `enableConfirmation` and `enableUnconfirmedLogin` module's properties. Instead you need to configure `UserConfirmation`
 service which got a few new options. More information on how to configure it can be found in [guide](docs/confirmation.md)

- `dektrium\user\helpers\Password` helper has been removed. If you need to generate password then use
 `dektrium\user\helpers\PasswordGenerator` helper instead. If you used it to hash and validate user's password, then
 use methods `hashPassword` and `validatePassword` of `dektrium\user\models\User`

- `cost` module's property has been removed. If you used custom cost value, then you should set it through 
 `passwordHashCost` property of `security` component.

- `enableFlashMessages` module's property has been removed. If you have used it, then you should simply remove it from
 your config. Moreover Yii2-user does not show flash messages.

- `modelMap` module's property has been removed. Models should be overridden using DI container. Read more in docs.

## Upgrade from Yii2-user 0.9.* to Yii2-user 0.9.4

- New authentication via social networks has been introduced. You should update
your `authClientCollection` component as described in [guide](docs/social-auth.md).

- Admin views have been remade. If you override admin view files, you should
update them accordingly to the made changes.

## Upgrade from Yii2-user 0.8.*

- **APPLY NEW MIGRATIONS!**

- `webUserClass` module option has been removed. If you use your own user 
component class you should set in `user` application component configuration:

```php
'components' => [
    'user' => [
        'class' => 'your\web\User',
    ],
],
```

- ModelManager component has been removed. If you override models, now you
should set them via `modelMap` module's property.

**Before:**

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        'components' => [
            'manager' => [
                'User' => 'your\model\User',
                'Profile' => 'your\model\Profile',
                ...
            ],
        ],
    ],
],
```

**After:**

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        'modelMap' => [
            'User' => 'your\model\User',
            'Profile' => 'your\model\Profile',
            ...
        ],
    ],
],
```

- Mailer component has been changed. Now it should be configured via `mailer`
module property. You can read more about mailer configuration [here](docs/mailer.md).

**Before:**

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        'components' => [
            'mailer' => [
                'sender' => 'noreply@myhost.com',
            ],
        ],
    ],
],
```

**After:**

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        'mailer' => [
            'sender' => 'noreply@myhost.com',
        ],
    ],
],
```

- Urls `user/settings/email` and `user/settings/password` have been merged into
a new one `user/settings/account`.