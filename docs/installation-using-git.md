# Installation using git

If you are going to contribute to Yii2-user or if you don't like composer, you can install Yii2-user using git.

## Cloning the source code

Change directory to @app/modules and run following command:

```bash
$ git clone git@github.com:dektrium/yii2-user.git user
```

## Making autoloading work

In order to make autoloading work you have to set an alias in your config file pointing to module directory:

```php
Yii::setAlias('@dektrium/user', '@app/modules/user');
```

## Setting up

In order to enable module you have to configure the Application::bootstrap property as follows:

```php
'bootstrap' => [
    'dektrium\user\Bootstrap'
],
```

## Applying migrations

The last thing you need to do is update your database schema by applying the migrations:

```bash
 $ php yii migrate/up --migrationPath=@app/modules/user/migrations
 ```

## Post-installation

Well done! You have completed the basic installation of Yii2-user, you are ready to learn about more advanced features and
usages of the module.

 - [Configuring the module](configuration.md)
 - [List of available actions](available-actions.md)
 - [User management](user-management.md)
