# Overriding default models

When you are creating application with Yii2-user you can find that you need to override models or forms. Yii2-user is
very extensible and allows you to override any model. Yii2-user does not create models using "new" statement, instead
it uses component named "Factory" which creates requested models. Here is default factory configuration:

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        'components' => [
            'manager' => [
                // Active record classes User and Profile
                'userClass' => 'dektrium\user\models\User',
                'profileClass' => 'dektrium\user\models\Profile',
                // Query classes for active record models
                'userQueryClass' => 'dektrium\user\models\UserQuery',
                'profileQueryClass' => 'yii\db\ActiveQuery',
                // Model that is used on resending confirmation messages
                'resendFormClass' => 'dektrium\user\forms\Resend',
                // Model that is used on logging in
                'loginFormClass' => 'dektrium\user\forms\Login',
                // Model that is used on password recovery
                'passwordRecoveryFormClass' => 'dektrium\user\forms\PasswordRecovery',
                // Model that is used on requesting password recovery
                'passwordRecoveryRequestFormClass' => 'dektrium\user\forms\PasswordRecoveryRequest',
            ],
        ],
    ],
],
```

> NOTE: User class should implement `dektrium\user\models\UserInterface`

### Example

Assume you decided to override user class and change registration process. Let's create new user class under `@app/models`.

```php
namespace app/models;

use dektrium\user\models\User as BaseUser;

class User extends BaseUser
{
    public function register()
    {
        // do your magic
    }
}
```

In order to make Yii2-user use your class you need to configure factory component as follows:

```php
'modules' => [
    'user' => [
        'class' => 'dektrium\user\Module',
        'components' => [
            'manager' => [
                'userClass' => 'app\models\User',
            ],
        ],
    ],
],
```

Well done! Yii2-user now uses your User model.
