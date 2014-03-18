<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\User $user
 */
$this->title = Yii::t('user', 'Register');
$this->params['breadcrumbs'][] = $this->title;
\dektrium\user\assets\Passfield::register($this);
$this->registerJs(sprintf('$("#user-password").passField({"locale": "%s"});', Yii::$app->language));
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin([
    'id' => 'registration-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($model, 'username') ?>

<?= $form->field($model, 'email') ?>

<?php if (!Yii::$app->getModule('user')->generatePassword): ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
<?php endif ?>

<?php if (in_array('register', Yii::$app->getModule('user')->captcha)): ?>
    <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
        'captchaAction' => 'user/default/captcha',
        'options' => ['class' => 'form-control'],
    ]) ?>
<?php endif ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(Yii::t('user', 'Register'), ['class' => 'btn btn-primary']) ?><br>
            <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
