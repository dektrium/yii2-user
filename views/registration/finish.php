<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 */

?>

<?php if (Yii::$app->session->hasFlash('user.password_generated')): ?>
    <div class="alert alert-info">
        <h4>
            <?= Yii::t('user', 'Password has been generated automatically') ?>
        </h4>
        <p>
            <?= Yii::t('user', 'We have generated password for you and sent to you via email') ?>.
            <?= Yii::t('user', 'The email can take a few minutes to arrive') ?>.
        </p>
    </div>
<?php endif ?>

<?php if (Yii::$app->session->hasFlash('user.registration_finished')): ?>
    <?php $this->title = Yii::t('user', 'Account has been created'); ?>
    <div class="alert alert-success">
        <h4>
            <?= Html::encode($this->title) ?>
        </h4>
        <?= Yii::t('user', 'Thank you for signing up on our website') ?>.
        <?= Yii::t('user', 'Your account has been created and you have been automatically logged in') ?>.
    </div>
<?php endif ?>

<?php if (Yii::$app->session->hasFlash('user.confirmation_sent')): ?>
    <?php $this->title = Yii::t('user', 'We need to confirm your email address'); ?>
    <div class="alert alert-info">
        <h4>
            <?= Html::encode($this->title) ?>
        </h4>
        <p>
            <?= Yii::t('user', 'Please check your email and click the confirmation link to complete your registration') ?>.
            <?= Yii::t('user', 'The email can take a few minutes to arrive') ?>.
            <?= Yii::t('user', 'But if you are having troubles, you can request a new one by clicking the link below') ?>:
        </p>
        <p>
            <?= Html::a(Yii::t('user', 'Request new confirmation message'), ['/user/registration/resend']) ?>
        </p>
    </div>
<?php endif ?>

<?php if (Yii::$app->session->hasFlash('user.invalid_token')): ?>
    <?php $this->title = Yii::t('user', 'Invalid token'); ?>
    <div class="alert alert-danger">
        <h4>
            <?= Html::encode($this->title) ?>
        </h4>
        <p>
            <?= Yii::t('user', 'We are sorry but your confirmation token is out of date') ?>.
            <?= Yii::t('user', 'You can try requesting a new one by clicking the link below') ?>:
        </p>
        <p>
            <?= Html::a(Yii::t('user', 'Request new confirmation message'), ['/user/registration/resend']) ?>
        </p>
    </div>
<?php endif ?>

<?php if (Yii::$app->session->hasFlash('user.confirmation_finished')): ?>
    <?php $this->title = Yii::t('user', 'Account has been confirmed'); ?>
    <div class="alert alert-success">
        <h4>
            <?= Html::encode($this->title) ?>
        </h4>
        <?= Yii::t('user', 'Awesome! You have successfully confirmed your email address. You may sign in using your credentials now') ?>
    </div>
<?php endif ?>
