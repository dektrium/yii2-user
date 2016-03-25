# Frequently Asked Questions

## How to change controller's layout?

You can change controller's layout using `controllerMap` module's property:

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        'controllerMap' => [
            'admin' => [
                'class'  => 'dektrium\user\controllers\AdminController',
                'layout' => '//admin-layout',
            ],
        ],
    ],
],
```