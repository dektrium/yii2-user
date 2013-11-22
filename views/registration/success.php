<?php
use yii\helpers\Html;

/**
 * @var yii\base\View $this
 */
$this->title = 'Sign up';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-register">
    <?php if (Yii::$app->getSession()->hasFlash('confirmation_message_sent')): ?>
        <div class="alert alert-info">
            <h4>Awesome, almost there! We need to confirm your email address</h4>
            Please check your email and click the confirmation link to complete your registration. If you\'re having troubles,
            you can resend it <?= Html::a('here', ['/user/registration/resend'])?>.
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            <h4>Awesome! Your account has been created</h4>
            You may log in now using your credentials <?= Html::a('here', ['/user/auth/login']) ?>.
        </div>
    <?php endif;?>
</div>
