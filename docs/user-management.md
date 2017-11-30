# User management

When you start using Yii2-user you will probably find that you need to create, update and delete accounts of other users.
In order to do that Yii2-user provides CRUD interface.

To start using user management interface you have to add your username to administrator's list as follows:


```php
...
'modules' => [
    ...
    'user' => [
        'class'  => 'dektrium\user\Module',
        'admins' => ['your-username-goes-here']
    ],
    ...
],
...
```

### Show users

Route **/user/admin/index** shows a list of registered users. You will be able to see a lot of useful information such
as registration time and ip address, confirmation and block status, etc.

### Create user

Route **/user/admin/create** shows create user form. To create a new user account you have to fill username and email
fields. If you do not want to create password for user leave password field empty, password will be generated automatically.
After create a welcome message will be sent to email that you have used to create user. It will contain user's credentials.

### Update user

Route **/user/admin/update** shows update user form. From that page you will be able to update account (email, username,
password) and profile (name, location, etc) information, block and confirm user. To access this route you should specify
id query parameter.

### Delete user

Route **/user/admin/delete** deletes an user account. To access this route you should specify id query parameter and do
a POST request. Be careful, you will not be able to restore deleted account.

### Impersonate User / Become another user

Route **/user/admin/switch** becomes an user for the current session. You need to be an administrator to use this
feature. Place something like this in your view file to allow to jump back when being impersonated as another person:

```
if (Yii::$app->session->has(\dektrium\user\controllers\AdminController::ORIGINAL_USER_SESSION_KEY))
    echo Html::a(
    '<span class="glyphicon glyphicon-user"></span> ' . Yii::t('main', 'Back to original user'),
     ['/user/admin/switch'], ['class' => 'btn btn-primary', 'data-method' => 'POST']);
```

or

```
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => [
        Yii::$app->session->has(\dektrium\user\controllers\AdminController::ORIGINAL_USER_SESSION_KEY) ?
        '<li>' . Html::beginForm(['/user/admin/switch'], 'post', ['class' => 'navbar-form'])
            . Html::submitButton('<span class="glyphicon glyphicon-user"></span> ' . Yii::t('user', 'Back to original user'),
                ['class' => 'btn btn-link']
            ) . Html::endForm() . '</li>' : '',
      ],
    ]);
```

You can declare module options 'adminPermission'. Access to action `switch` must be prefaced to all:
```
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'adminPermission' => 'administrateUser',
        ],
    ],
```
```
    'modules' => [
        'user' => [
            'controllerMap' => [
                'admin' => [
                    'class' => 'dektrium\user\controllers\AdminController',
                    'as access' => [
                        'class' => 'yii\filters\AccessControl',
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['administrateUser'],
                            ],
                            [
                                'actions' => ['switch'],
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
```


##Changing CRUD layout

Sometimes you will need to have different layouts for frontend and backend pages. It is really easy to change admin layout:

```php
...
'modules' => [
    ...
    'user' => [
        'class' => 'dektrium\user\Module',
        'controllerMap' => [
            'admin' => [
                'class'  => 'app\controllers\user\AdminController',
                'layout' => 'path-to-your-admin-layout',
            ],
        ],
        ...
    ],
    ...
],
...
```
