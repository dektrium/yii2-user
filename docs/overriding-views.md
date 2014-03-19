Overriding default views
========================

When you start using Yii2-user you will probably find that you need to override the default views provided by the module.
In order to do this you should configure view application component as follows:

```php
'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@vendor/dektrium/yii2-user/views' => '@app/views/user'
            ],
        ],
    ],
],
```

> **NOTE:** If you installed module using git as described in installation guide, you should use `@app/modules/user/views` instead of `@vendor/dektrium/yii2-user/views`

In the above pathMap defines where to look for view files. For example, if pathMap contains `'@vendor/dektrium/yii2-user/views' => '@app/views/user'`,
then the themed version for a view file /vendor/dektrium/yii2-user/views/auth/login.php will be /views/user/auth/login.php.