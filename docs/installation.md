# Getting started

First of all you need to install module. There are two ways of installing Yii2-user: the easiest way using composer and
the nerdy way using git (for developers). This guide describes installing using composer because it is really easy, does
not take much time and is suitable in most cases. If you are going to contribute to Yii2-user you'd better install it
using [git](installation-using-git.md).

## Installation

Either run following command:

```bash
$ php composer.phar require --prefer-dist dektrium/yii2-user "dev-master"
```

or add

```json
"dektrium/yii2-user": "dev-master"
```

to the require section of your `composer.json` file and run following command:

```bash
$ php composer.phar update
```

## Applying migrations

After you installed Yii2-user, the last thing you need to do is update your database schema by applying the migrations:

```bash
$ php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
```

## Post-installation

Now that you have completed the basic installation of Yii2-user, you are ready to learn about more advanced features and
usages of the module.

- [Configuring the module](configuration.md)
- [List of available actions](available-actions.md)
- [User management](user-management.md)
