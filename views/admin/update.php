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
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                 $this
 * @var dektrium\user\models\User    $user
 * @var dektrium\user\models\Profile $profile
 * @var dektrium\user\Module         $module
 */

$this->title = Yii::t('user', 'Update user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', [
    'module' => Yii::$app->getModule('user'),
]) ?>

<div class="alert alert-info">
    <?= Yii::t('user', 'Registered at {0, date, MMMM dd, YYYY HH:mm} from {1}', [$user->created_at, is_null($user->registration_ip) ? 'N/D' : $user->registration_ip]) ?>
</div>
<?php if ($module->enableConfirmation && $user->getIsConfirmed()): ?>
    <div class="alert alert-success">
        <?= Yii::t('user', 'Confirmed at {0, date, MMMM dd, YYYY HH:mm}', [$user->created_at]) ?>
    </div>
<?php endif; ?>
<?php if ($user->getIsBlocked()): ?>
    <div class="alert alert-danger">
        <?= Yii::t('user', 'Blocked at {0, date, MMMM dd, YYYY HH:mm}', [$user->blocked_at]) ?>
    </div>
<?php endif;?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false
]); ?>

<div class="panel panel-default">
    <div class="panel-body">
        <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if (!$user->getIsConfirmed()): ?>
            <?= Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post']) ?>
        <?php endif; ?>
        <?php if ($user->getIsBlocked()): ?>
            <?= Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post', 'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')]) ?>
        <?php else: ?>
            <?= Html::a(Yii::t('user', 'Block'), ['block', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'post', 'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')]) ?>
        <?php endif; ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Html::encode($this->title) ?>
    </div>
    <div class="panel-body">
        <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('user', 'Update user profile') ?>
    </div>
    <div class="panel-body">
        <?= $this->render('_profile', ['form' => $form, 'profile' => $profile]) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-primary btn-sm']) ?>
        <?php if (!$user->getIsConfirmed()): ?>
            <?= Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post']) ?>
        <?php endif; ?>
        <?php if ($user->getIsBlocked()): ?>
            <?= Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-success btn-sm', 'data-method' => 'post', 'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')]) ?>
        <?php else: ?>
            <?= Html::a(Yii::t('user', 'Block'), ['block', 'id' => $user->id, 'back' => 'update'], ['class' => 'btn btn-danger btn-sm', 'data-method' => 'post', 'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?')]) ?>
        <?php endif; ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
