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

<?php if (Yii::$app->session->hasFlash('user.invalid_token')): ?>
    <?php $this->title = Yii::t('user', 'Recovery token is invalid'); ?>
    <div class="alert alert-danger">
        <h4>
            <?= Html::encode($this->title) ?>
        </h4>
        <p>
            <?= Yii::t('user', 'We are sorry but your recovery token is out of date') ?>.
            <?= Yii::t('user', 'You can try requesting a new one by clicking the link below') ?>:
        </p>
        <p>
            <?= Html::a(Yii::t('user', 'Request new recovery message'), ['/user/recovery/request']) ?>
        </p>
    </div>
<?php endif ?>

<?php if (Yii::$app->session->hasFlash('user.recovery_finished')): ?>
    <?php $this->title = Yii::t('user', 'Password has been reset'); ?>
    <div class="alert alert-success">
        <h4>
            <?= Html::encode($this->title) ?>
        </h4>
        <p>
            <?= Yii::t('user', 'Your password has been successfully changed. You can try logging in using your new password') ?>
        </p>
    </div>
<?php endif ?>

<?php if (Yii::$app->session->hasFlash('user.recovery_sent')): ?>
    <?php $this->title = Yii::t('user', 'Recovery message sent'); ?>
    <div class="alert alert-success">
        <h4>
            <?= Html::encode($this->title) ?>
        </h4>
        <p>
            <?= Yii::t('user', 'You have been sent an email with instructions on how to reset your password.') ?>
            <?= Yii::t('user', 'Please check your email and click the reset link.') ?>
        </p>
        <p>
            <?= Yii::t('user', 'The email can take a few minutes to arrive. But if you are having troubles, you can request a new one.') ?>
        </p>
    </div>
<?php endif ?>