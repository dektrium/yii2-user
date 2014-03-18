<?php
use yii\helpers\Html;

/**
 * @var yii\base\View $this
 * @var dektrium\user\models\User $model
 */
$this->title = Yii::$app->getModule('user')->confirmable ?
    Yii::t('user', 'Confirmation needed') :
    Yii::t('user', 'Account has been created');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (Yii::$app->getModule('user')->confirmable): ?>
    <div class="alert alert-info">
        <h4><?= Yii::t('user', 'Awesome, almost there! We need to confirm your email address') ?></h4>
        <?= Yii::t('user', 'Please check your email and click the confirmation link to complete your registration.') ?>
        <?= Yii::t('user', 'If you\'re having troubles, you can resend it by clicking the link below:') ?>
        <br>
        <?= Html::a(Yii::t('user', 'Request new confirmation message'), ['/user/registration/resend']) ?>
    </div>
<?php else: ?>
    <div class="alert alert-success">
        <h4><?= Yii::t('user', 'Awesome! Your account has been created') ?></h4>
        <?= Yii::t('user', 'You can log in using your credentials now') ?>.
    </div>
<?php endif; ?>
