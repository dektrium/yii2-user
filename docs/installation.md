Installation
============

This document will guide you through the process of installing Yii2-user using **composer**. Installation is a quick and
easy three-step process.

> **NOTE:** Before we start make sure that you have properly configured **db** and **mail** application components.


Step 1: Downloading Yii2-user using composer
--------------------------------------------

Add Yii2-user to the require section of your **composer.json** file:

```js
{
    "require": {
        "dektrium/yii2-user": "dev-master"
    }
}
```

And run following command to make **composer** download and install Yii2-user:

```bash
$ php composer.phar update
```

Step 2: Configuring your application
------------------------------------

Add following lines to your main configuration file:

```php
...
'modules' => [
    ...
    'user' => [
        'class' => 'dektrium\user\Module',
    ],
    ...
],
...
```

Step 3: Updating database schema
--------------------------------

After you downloaded and configured Yii2-user, the last thing you need to do is updating your database schema by applying
the migrations:

```bash
$ php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
```
