# Getting started with Yii2-user

Yii2-user is designed to work out of the box. It means that installation requires
minimal steps. Only one configuration step should be taken and you are ready to
have user management on your Yii2 website.

> If you're using Yii2 advanced template, you should read [this article](usage-with-advanced-template.md) firstly.

### 1. Download

Yii2-user can be installed using composer. Run following command to download and
install Yii2-user:

```bash
composer require "dektrium/yii2-user:0.9.*@dev"
```

### 2. Configure

> **NOTE:** Make sure that you don't have `user` component configuration in your config files.

Add following lines to your main configuration file:

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
    ],
],
```

### 3. Update database schema

The last thing you need to do is updating your database schema by applying the
migrations. Make sure that you have properly configured `db` application component
and run the following command:

```bash
$ php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
```

## Where do I go now?

You have Yii2-user installed. Not you can check out the [list of articles](README.md)
for more information.

## Troubleshooting

If you're having troubles with Yii2-user, make sure to check out the 
[troubleshooting guide](troubleshooting.md).
