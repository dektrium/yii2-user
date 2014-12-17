Upgrading instructions for Yii2-user
====================================

The following upgrading instructions are cumulative. That is, if you want to upgrade from version A to version C and
there is version B between A and C, you need to following the instructions for both A and B.

Upgrade from Yii2-user 0.8.0
----------------------------

- `webUserClass` module option has been dropped. If you are using custom user component you should set in `user`
application component configuration:

```php
'components' => [
    'user' => [
        'class' => 'your\web\User',
    ],
],
```

- ModelManager component has been dropped. If you are using custom models you should set them via `modelMap` module's
property:

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

- Mailer component has been refactored. If you are using custom mailer or changing its configuration, you should set it
via `mailer` module's property:

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
