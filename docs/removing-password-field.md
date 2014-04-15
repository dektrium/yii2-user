# How to: Removing password field from registration form

In some projects you may need to remove password field from registration form and generate password automatically and
send it to the user by email. In this howto I would like to show you how you can implement this in four steps:

1. Removing password field from safe attributes
2. Generating password
3. Sending password by email
4. Updating registration form

## Before we start

First of all you need to override User model and view files as described in special guides.

## Step 1: Removing password field from safe attributes

Open `app\models\User` class and add following method in order to override `register` scenario:

```php
/**
 * @inheritdoc
 */
public function scenarios()
{
    $scenarios = parent::scenarios();
    $scenarios['register'] = ['username', 'email'];
    return $scenarios;
}
```

## Step 2: Generating password

As we removed password field we need to generate it. Best place for generating password is `beforeValidate` method. But
how will we generate password? Yii2-user has special `Password` helper that contains `generatePassword` method. It
generates user-friendly random password containing at least one lower case letter, one uppercase letter and one digit.

```php
/**
 * @inheritdoc
 */
public function beforeValidate()
{
    if (parent::beforeValidate()) {
        // we only need to generate password if scenario is "register"
        if ($this->scenario == 'register') {
            // generate password with length 6
            $this->password = Password::generate(6);
        }
        return true;
    }
    return false;
}
```

## Step 3: Sending password by email

To deliver generated password to user we have to send user a welcome message which will include generated password. To
do this we have to hook into registration process. Add following method to your User model:

```php
public function afterRegister()
{
    \Yii::$app->mail->compose('welcome', ['user' => $this])
        ->setFrom('no-reply@example.com')
        ->setTo($this->email)
        ->setSubject('Welcome to our site')
        ->send();
    parent::afterRegister();
}
```

Let's create a mail view that will be sent to user. Create and open `@app/mails/welcome.php` and paste there following code:

```php
<?php

/**
 * @var app\models\User $user
 */
?>
<p>
    Hi there! You have recently registered on our site and we have generated password for you. You can use it with your
    email address in order to log in.
</p>
<p>
    Email: <?= $user->email ?><br>
    Username: <?= $user->username ?><br>
    Password: <?= $user->password ?>
</p>
```

## Step 4: Updating registration form

Last step is removing password field from registration form. It is pretty easy: create and open `@app/views/user/registration/register.php`
and paste there following code:

```php
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\User $user
 */
$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p>Password will be generated automatically and delivered you by email</p>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'registration-form',
                ]); ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'email') ?>

                <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
        </p>
    </div>
</div>
```
