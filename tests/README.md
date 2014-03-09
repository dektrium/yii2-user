Yii2-user tests
===============

## Directory structure

	_fixtures/           Fixtures to be applied within tests
	_helpers/            Helpers
	_log/                Logs
	_pages/              Pages classes that used within functional tests
	functional/          Functional tests to run with Codeception
	unit/                Unit tests to run with Codeception

## Testing

Install and run [Mailcatcher](http://mailcatcher.me/). After that install additional composer packages:

```bash
$ php composer.phar require --dev "codeception/codeception: 1.8.*@dev" "codeception/specify: *" "codeception/verify: *"
```

Database is used in testing, so you should create database that is used in tests. To make your database up to date,
you can run in test folder following command:

```bash
$ php yii migrate/up --migrationPath=migrations
```

After that is done you should be able to run your tests:

```bash
$ ../../vendor/bin/codecept build
$ ../../vendor/bin/codecept run
```