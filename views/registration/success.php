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
 * @var dektrium\user\models\User $model
 */

$this->title = Yii::$app->getModule('user')->confirmable ?
    Yii::t('user', 'Confirmation needed') :
    Yii::t('user', 'Your account has been created');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (Yii::$app->getModule('user')->confirmable): ?>
    <div class="alert alert-info">
        <h4><?= Yii::t('user', 'Awesome, almost there! We need to confirm your email address.') ?></h4>
        <?= Yii::t('user', 'Please check your email and click the confirmation link to complete your registration.') ?>
        <?= Yii::t('user', 'The email can take a few minutes to arrive. But if you are having troubles, you can request a new one.') ?>
        <?= Html::a(Yii::t('user', 'Request new confirmation message'), ['/user/registration/resend']) ?>
    </div>
<?php else: ?>
    <div class="alert alert-success">
        <h4><?= Html::encode($this->title) ?></h4>
        <?= Yii::t('user', 'Thank you for registration on our website. You may sign in using your credentials.') ?>
    </div>
<?php endif; ?>
