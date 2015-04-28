# Overriding views

When you start using Yii2-user you will probably find that you need to override the default views provided by the module.
Although view names are not configurable, Yii2 provides a way to override views using themes. To get started you should
configure your view application component as follows:

```php
...
'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@dektrium/user/views' => '@app/views/user'
            ],
        ],
    ],
],
...
```

In the above `pathMap` means that every view in @dektrium/user/views will be first searched under `@app/views/user` and
if a view exists in the theme directory it will be used instead of the original view.

## Example

An example of overriding the registration page view is demonstrated below. First make sure you have configured view
application component.

In order to override the registration view file you should create `@app/views/user/registration/register.php`. Open it
and paste in the following code:

```php
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View              $this
 * @var yii\widgets\ActiveForm    $form
 * @var dektrium\user\models\User $user
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-success">
    <p>This view file has been overriden!</p>
</div>
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

Then open registration page and make sure that you see **'This view file has been overrided!'**. If you don't see it
make sure you have properly configured your view component and created view file in right location.
