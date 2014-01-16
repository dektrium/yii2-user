Yii2-User
=========

Yii2-User is a flexible user management module for Yii2 that handles common tasks such as registration, authentication
and password retrieval.

Features include:

* Registration support, with an optional confirmation per mail
* Authentication support
* Password recovery support
* Console commands
* Unit and functional tests

**NOTE:** Module is in initial development. Anything may change at any time.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require dektrium/yii2-user "~0.3@dev"
```

or add

```js
{
    "require": {
        "dektrium/yii2-user": "~0.3@dev"
    }
}
```

to the require section of your `composer.json` file.

## Usage

Once the extension is installed, simply run migrations:

```bash
$ php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
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