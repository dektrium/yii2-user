Upgrading instructions for Yii2-user
====================================

The following upgrading instructions are cumulative. That is, if you want to
upgrade from version A to version C and there is version B between A and C, you
need to following the instructions for both A and B.

Upgrade from Yii2-user 0.9.* to Yii2-user 0.9.4

- New authentication via social networks has been introduced. You should update
your `authClientCollection` component as described in [guide](docs/social-auth.md).

- Admin views have been remade. If you override admin view files, you should
update them accordingly to the made changes.

Upgrade from Yii2-user 0.8.*
----------------------------

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