# How to add captcha to form

Adding captcha to forms is pretty easy and can be done in three steps:

1. Add new field and validation rule in the model
2. Show captcha in view
3. Add captcha action in controller

In this guide I would like to show you how to add captcha field in the registration form but you can add captcha
to any form following this steps.

> **NOTE:** Captcha will not work with ajax validation enabled
 ([see](https://github.com/yiisoft/yii2/blob/master/framework/captcha/CaptchaValidator.php#L20)).
 You should not enable it in active form configuration array.

## 1. Adding field and validation rules to model

First of all you need to override Registration form as described in special guide. After this done you have to add
public property named **captcha** and validation rules.


```php

    <?php

    namespace app\models;

    class RegistrationForm extends \dektrium\user\models\RegistrationForm
    {
        /**
         * @var string
         */
        public $captcha;
        /**
         * @inheritdoc
         */
        public function rules()
        {
            $rules = parent::rules();
            $rules[] = ['captcha', 'required'];
            $rules[] = ['captcha', 'captcha'];
            return $rules;
        }
    }
    
```

## 2. Adding widget to the view

Before doing this step you have to configure view application component as described in guide. After this done you have
to create new file named `register.php` in `@app/views/user/registration`. Now you have to add widget to registration
form, just copy and paste following code into newly created view file.

```php

    <?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\captcha\Captcha;

    /**
     * @var yii\web\View $this
     * @var yii\widgets\ActiveForm $form
     * @var app\models\RegistrationForm $model
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
                    <?php $form = ActiveForm::begin([
                        'id' => 'registration-form',
                    ]); ?>

                    <?= $form->field($model, 'username') ?>

                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <?= $form->field($model, 'captcha')->widget(Captcha::className(), [
                        'captchaAction' => ['/site/captcha']
                    ]) ?>

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


## 3. Adding action to the controller

In order to make captcha work you have to add captcha action to `app\controllers\SiteController` Maybe it is already
added because standard Yii2 application template adds it automatically.

```php

    <?php
    
    namespace app\controllers;

    class SiteController extends \yii\web\Controller
    {
        ...
        public function actions()
        {
            return [
                'captcha' => [
                    'class' => 'yii\captcha\CaptchaAction',
                ],
            ];
        }
        ...
    }
    
```