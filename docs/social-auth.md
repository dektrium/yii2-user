Authentication via social networks
==================================

Yii2-user provides a way to use social networks to authenticate users on your website. There is a way for users to connect
their social network account to their account on your website and use it for authentication.

Getting started
---------------

To get started you should configure `authClientCollection` application component. You can get more information about it
in Yii2-authclient extension's [documentation](https://github.com/yiisoft/yii2-authclient).

```php
...
'components' => [
    ...
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'google' => [
                'class' => 'yii\authclient\clients\GoogleOpenId'
            ],
            'facebook' => [
                'class' => 'yii\authclient\clients\Facebook',
                'clientId' => 'facebook_client_id',
                'clientSecret' => 'facebook_client_secret',
            ],
        ],
    ],
    ...
],
...
```

How it works
------------

When you are going to log in you can click social network icon. If you have already logged in using that account you
will be logged in. Otherwise you will be shown simple sign up form with two field (username and email).

After you logged in you can go to accounts settings page and connect new account or disconnect already connected accounts.
