# Yii2-user with Yii2 advanced template

When using advanced template, you may want to have AdminController only available
in backend, and all other controllers available in frontend. This guide will
help you with implementing this.

## Install

Install module as described in [getting started](getting-started.md) guide, without
configuring module as described in step 2.

## Configure application

Let's start with defining module in `@common/config/main.php`:

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        // you will configure your module inside this file
        // or if need different configuration for frontend and backend you may
        // configure in needed configs
    ],
],
```

Now we should restrict access to admin controller in frontend application. Open
`@frontend/config/main.php` and add following:

```
'modules' => [
    'user' => [
        // following line will restrict access to admin page
        'as frontend' => 'dektrium\user\filters\FrontendFilter',
    ],
],
```

Also do the same thing with `@backend/config/main.php`:

```
'modules' => [
    'user' => [
        // following line will restrict access to admin page
        'as backend' => 'dektrium\user\filters\BackendFilter',
    ],
],
```

That's all, now you have module installed and configured in advanced template.

## Use independent sessions in one domain

If you have frontend and backend apps in one domain (e.g. domain.com and domain.com/admin),
sometimes you may need to have independent sessions for them, which means that
if you log in on frontend, you will not be logged in on backend.

Configure `@backend\config\main.php`:

```php
'components' => [
    'user' => [
        'identityCookie' => [
            'name'     => '_backendIdentity',
            'path'     => '/admin',
            'httpOnly' => true,
        ],
    ],
    'session' => [
        'name' => 'BACKENDSESSID',
        'cookieParams' => [
            'httpOnly' => true,
            'path'     => '/admin',
        ],
    ],  
],
```

Then configure `@frontend\config\main.php`:

```php
'components' => [
    'user' => [
        'identityCookie' => [
            'name'     => '_frontendIdentity',
            'path'     => '/',
            'httpOnly' => true,
        ],
    ],
    'session' => [
        'name' => 'FRONTENDSESSID',
        'cookieParams' => [
            'httpOnly' => true,
            'path'     => '/',
        ],
    ],  
],
```

From now you have two different sessions for frontend and backend.