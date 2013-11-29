Yii2-User
=========

Yii2-User is a flexible user registration and authentication module for Yii2. It provides user authentication and registration to your Yii2 site.

**NOTE:** Module is in initial development. Anything may change at any time.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require dektrium/yii2-user "0.1.0"
```

or add

```
"dektrium/yii2-user": "0.1.0"
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
	    ...
		'user' => 'dektrium\user\Module',
		...
	],
	...
	'components' => [
	    ...
	    'user' => [
	        'class' => 'dektrium\user\components\User',
	    ],
	    ...
	]
];
```

## License

Dektrium user module is released under the MIT License. See the bundled `LICENSE.md` for details.

## What is Dektrium?

The goal of Dektrium project is to provide useful extensions to your Yii2 application to make development easier.