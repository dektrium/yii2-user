Installation
============

This document will guide you through the process of installing Yii2-user using **composer**. Installation is a quick
and easy three-step process.

Step 1: Download Yii2-user using composer
-----------------------------------------

Add `"dektrium/yii2-user": "0.9.*@dev"` to the require section of your **composer.json** file and run
`composer update` to download and install Yii2-user.

Step 2: Configure your application
------------------------------------

> **NOTE:** Make sure that you don't have any `user` component configuration in your config files.

Add following lines to your main configuration file:

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
    ],
],
```

Step 3: Update database schema
------------------------------

> **NOTE:** Make sure that you have properly configured **db** application component.

After you downloaded and configured Yii2-user, the last thing you need to do is updating your database schema by
applying
the migrations:

```bash
$ php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
```

FAQ
---

**Installation failed. There are no files in `vendor/dektrium/yii2-user`**

*Try removing Yii2-user version constraint from composer.json, then run `composer update`. After composer finish
 removing of Yii2-user, re-add version constraint and `composer update` again.*

**I can't log in. After clicking login button it just redirects without logging me in.**

*You should remove `user` component configuration from your config files.*
