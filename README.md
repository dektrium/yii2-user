Dektrium User Module
==================

Dektrium User Module is a flexible user registration and authentication module for Yii2. It provides user authentication and registration to your Yii2 site.

**NOTE:** Module is in initial development. Anything may change at any time.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require dektrium/yii2-user "*"
```

or add

```
"dektrium/yii2-user": "dev-master"
```

to the require section of your `composer.json` file.

## Usage

Once the extension is installed, simply run migrations:

```bash
$ php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/dektrium/user/migrations
```

And modify your application configuration as follows:

```php
return [
	'modules' => [
		'user' => 'dektrium\user\WebModule',
		...
	],
	...
	'components' => [
	    ...
	    'user' => [
	        'class' => 'yii\web\User',
	        'identityClass' => 'dektrium\user\models\User',
	        'loginUrl' => ['/user/auth/login']
	    ],
	    ...
	]
];
```

## License

Dektrium user module is released under the MIT License. See the bundled `LICENSE.md` for details.

## What is Dektrium?

The goal of Dektrium project is to provide useful modules to your Yii2 application to make development easier.