Testing with Codeception
========================
I'm using Yii2 basic template so the guide will depend on yii2-app-basic directory structure. If you're using advanced template - you're on your own.

Install/configure Codeception
-----------------------------
There is a [short guide](http://www.yiiframework.com/doc-2.0/guide-test-environment-setup.html) in the official documentation which will get you started with Codeception in Yii2.
You will also need to setup test database and apply yii2-user-migrations against it.
- Create new database, add new user for it or give db access to your existing database user. Lets say your new database is called "yii2_test" (damn I'm good with names)
- In your @app/tests/codeception/config/config.php change the dbname to "yii2_test"
- Apply yii2-user migrations to test database with
```bash
$ php /path/to/yii/app/tests/codeception/bin/yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations
```
- Default app configuration get merged and overwritten by this config.php when you're testing your app
- Now add this part to the same @app/tests/codeception/config/config.php
```php
...
'modules' => [
    'user' => [
        'admins' => ['user'],
        'mailer' => [
            'class' => 'app\components\MailerMock',
        ],
    ],
],
...
```
- This one is copied from @vendor/dektrium/yii2-user/tests/codeception/app/config/web.php. Without adding "user" as admin - user admin interface tests will fail (no users have admin rights to manage other users)


Copying source tests
--------------------
Copy page objects, fixtures, unit and functional tests.
Please replace "/path/to/yii/app" to path in your system. Also make note, that i'm using default vendor folder which is @app/vendor.
Also make note of codeception helpers path, which is set in @app/tests/codeception.yml.

```bash
$ cp /path/to/yii/app/vendor/dektrium/yii2-user/tests/codeception/_pages/* /path/to/yii/app/tests/codeception/_pages
$ cp /path/to/yii/app/vendor/dektrium/yii2-user/tests/codeception/_support/* /path/to/yii/app/tests/codeception/_support # your helpers path
$ cp -r /path/to/yii/app/vendor/dektrium/yii2-user/tests/codeception/fixtures/* /path/to/yii/app/tests/codeception/fixtures
$ cp /path/to/yii/app/vendor/dektrium/yii2-user/tests/codeception/functional/*Cept.php /path/to/yii/app/tests/codeception/functional
$ cp /path/to/yii/app/vendor/dektrium/yii2-user/tests/codeception/unit/*Cept.php /path/to/yii/app/tests/codeception/unit
$ cp /path/to/yii/app/vendor/dektrium/yii2-user/tests/codeception/app/components/MailerMock.php /path/to/yii/app/components
```
You can supply "-i" flag to cp command if you dont want to overwrite existing files.

We're almost done. Now we need to enable this helpers we just copied.
Open "/path/to/yii/app/tests/codeception/functional.suite.yml"
```
enabled:
    - Filesystem
    - Yii2
```
Add 
```
- tests\codeception\_support\FixtureHelper
- tests\codeception\_support\MailHelper
```
to enabled section

Open "/path/to/yii/app/tests/codeception/unit.suite.yml".
Add
```
modules:
    enabled: [Asserts]
```

Now rebuild your actors
```bash
# in your yii app folder run
$ codecept --config=tests/codeception.yml build
# you should see that number of methods increased in your UnitTester and FunctionalTester classes
```

And run!
```bash
# in your yii app folder
$ codecept --config=tests/codeception.yml run
```


Final words
-----------
Your login test (and few other) will still fail. MWAHAHAHA. They expect for "logout" text to be present on the final page. On most sites, logout link is present in the header, so I'm sure you can cope with this.

Finally. This guide was written on clean yii2 with only yii2-user module installed. As your site will evolve you will need to refactor your tests.