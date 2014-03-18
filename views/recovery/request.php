<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\base\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\forms\Recovery $model
 */
$this->title = Yii::t('user', 'Password recovery');
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin([
    'id' => 'recovery-form',
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?= $form->field($model, 'email') ?>

<?php if (in_array('recovery', Yii::$app->getModule('user')->captcha)): ?>
    <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
        'captchaAction' => 'user/default/captcha',
        'options' => ['class' => 'form-control'],
    ]) ?>
<?php endif ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(Yii::t('user', 'Send'), ['class' => 'btn btn-primary']) ?><br>
        </div>

    </div>

<?php ActiveForm::end(); ?>
