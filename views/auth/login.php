<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\forms\Login $model
 */
$this->title = Yii::t('user', 'Log in');
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($model, 'login') ?>

<?= $form->field($model, 'password')->passwordInput() ?>

<?php if (in_array('login', Yii::$app->getModule('user')->captcha)): ?>
    <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
        'captchaAction' => 'user/default/captcha',
        'options' => ['class' => 'form-control'],
    ]) ?>
<?php endif ?>

<?= $form->field($model, 'rememberMe', [
    'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
])->checkbox() ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(Yii::t('user', 'Log in'), ['class' => 'btn btn-primary']) ?><br>
            <?= Html::a(Yii::t('user', 'Forgot password?'), ['/user/recovery/request']) ?> |
            <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
        </div>

    </div>

<?php ActiveForm::end(); ?>
