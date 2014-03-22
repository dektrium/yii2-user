# Installation

This page explains the ways of installing User module. There are two ways to install:

- The easiest way using composer
- The nerdy way using git

After you have done this go to the section `Setting up`

### Installing via composer

Either run following command:

```bash
$ php composer.phar require dektrium/yii2-user "*"
```

or add

```js
{
    "require": {
        "dektrium/yii2-user": "*"
    }
}
```

to the require section of your `composer.json` file and run following command:

```bash
$ php composer.phar update
```

### Installing via git

Change directory to `@app/modules` and run following command:

```bash
$ git clone git@github.com:dektrium/yii2-user.git user
```

To make autoloading work you should set following alias pointing to module directory in your config file:

```php
Yii::setAlias('@dektrium/user', __DIR__.'/../modules/user');
```

## Setting up

To enable module you should configure your application as follows:

```php
'modules' => [
	...
	'user' => [
	    'class' => 'dektrium\user\Module',
	],
	...
],
...
'components' => [
	...
	'user' => [
	    'class' => 'dektrium\user\components\User'
	],
	...
],
```

## Updating database schema

After you configured module, the last thing you need to do is update your database schema by running the migrations:

```bash
$ php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
```

OR if you used git for installation:

```bash
$ php yii migrate/up --migrationPath=@app/modules/user/migrations
```

## Post-installation

### Emails

If you are going to use confirmable and recoverable features you should configure your mail component as follows:

```php
'components' => [
	...
	'mail' => [
		'class' => '\yii\swiftmailer\Mailer',
		'transport' => [
			// transport configuration
		],
		'messageConfig' => [
			// this option must be set
			'from' => 'noreply@mydomain.com',
		]
	]
	...
]
```

### URL Management

If your application enables pretty urls you may need to add following rules at the beginning of your URL rule set in your application configuration:

```php
'rules' => [
	'register' => 'user/registration/register',
	'resend' => 'user/registration/resend',
	'confirm/<id:\d+>/<token:\w+>' => 'user/registration/confirm',
	'login' => 'user/auth/login',
	'logout' => 'user/auth/logout',
	'recovery' => 'user/recovery/request',
	'reset/<id:\d+>/<token:\w+>' => 'user/recovery/reset',
	...
],
```