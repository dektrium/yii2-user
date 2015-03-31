Authentication via social networks
==================================

Yii2-user provides a way to use social networks to authenticate users on your
website. There is a way for users to connect their social network account to
their account on your website and use it for authentication.

Getting started
---------------

To get started you should configure `authClientCollection` application component.
You can get more information about it in Yii2-authclient extension's
[documentation](https://github.com/yiisoft/yii2-authclient).

```php
...
'components' => [
    ...
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            // here is the list of clients you want to use
            // you can read more in the "Available clients" section
        ],
    ],
    ...
],
...
```

Available clients
-----------------

Here is the list of clients supported by the module:

- Facebook
- Twitter
- Google
- Github
- VKontakte
- Yandex

### Facebook

- You can register new application and get secret keys [here](https://developers.facebook.com/apps).

```php
'facebook' => [
    'class'        => 'dektrium\user\clients\Facebook',
    'clientId'     => 'APP_ID',
    'clientSecret' => 'APP_SECRET',
],
```

### Twitter

- You can register new application and get secret keys [here](https://dev.twitter.com/apps/new).

> NOTE: Current version of Twitter API does not provide user's email address, so we can't register user without making him enter his email address

```php
'twitter' => [
    'class'          => 'dektrium\user\clients\Twitter',
    'consumerKey'    => 'CONSUMER_KEY',
    'consumerSecret' => 'CONSUMER_SECRET',
],
```

### Google

- First of all you need to enable Google+ API in `APIs & auth` section.
- Then you need to create new client id on `APIs & auth > Credentials` section
- `Authorized JavaScript origins` should contain url like `http://localhost`
- `Authorized redirect URIs` should contain url like `http://localhost/user/security/auth?authclient=google`

```php
'google' => [
    'class'        => 'dektrium\user\clients\Google',
    'clientId'     => 'CLIENT_ID',
    'clientSecret' => 'CLIENT_SECRET',
],
```

### Github

- You can register new application and get secret keys [here](https://github.com/settings/applications/new).

```php
'github' => [
    'class'        => 'dektrium\user\clients\GitHub',
    'clientId'     => 'CLIENT_ID',
    'clientSecret' => 'CLIENT_SECRET',
],
```

### VKontakte

- You can register new application and get secret keys [here](http://vk.com/editapp?act=create).

```php
'vkontakte' => [
    'class'        => 'dektrium\user\clients\VKontakte',
    'clientId'     => 'CLIENT_ID',
    'clientSecret' => 'CLIENT_SECRET',
]
```

### Yandex

- You can register new application and get secret keys [here](https://oauth.yandex.com/client/new).
- Make sure that you have enabled access to email address in `Yandex.Passport API` section.
- Also you should set the callback url to url like `http://localhost/user/security/auth?authclient=yandex`.

```php
'yandex' => [
    'class'        => 'dektrium\user\clients\Yandex',
    'clientId'     => 'CLIENT_ID',
    'clientSecret' => 'CLIENT_SECRET'
],
```
