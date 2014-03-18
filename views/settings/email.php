<?php

use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\User $model
 */
$this->title = Yii::t('user', 'Email settings');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <?php if (Yii::$app->getSession()->hasFlash('settings_saved')): ?>
        <div class="col-md-12">
            <div class="alert alert-success">
                <?= Yii::$app->getSession()->getFlash('settings_saved') ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::$app->getUser()->getIdentity()->username ?></h3>
            </div>
            <div class="panel-body">
                <?= \yii\widgets\Menu::widget([
                    'options' => [
                        'class' => 'nav nav-pills nav-stacked'
                    ],
                    'items' => [
                        ['label' => Yii::t('user', 'Profile'), 'url' => ['/user/settings/profile']],
                        ['label' => Yii::t('user', 'Email'), 'url' => ['/user/settings/email']],
                        ['label' => Yii::t('user', 'Password'), 'url' => ['/user/settings/password']]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Yii::t('user', 'Email settings') ?>
            </div>
            <div class="panel-body">
                <?php if (!empty($model->unconfirmed_email)): ?>
                    <div class="alert alert-warning"><?= Yii::t('user', 'Before your email will be changed we need you to confirm your new email address') ?>
                        <?= \yii\helpers\Html::a(Yii::t('user', 'Cancel email change'), Url::to(['reset']), ['class' => 'btn btn-danger btn-xs', 'data-method' => 'post']) ?>
                    </div>
                <?php endif; ?>
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'id' => 'profile-form',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                ]); ?>

                <div class="form-group">
                    <label class="col-lg-3 control-label"><?= Yii::t('user', 'Current email') ?></label>
                    <div class="col-lg-9">
                        <p class="form-control-static"><?= $model->email ?></p>
                    </div>
                </div>

                <?= $form->field($model, 'unconfirmed_email') ?>

                <?= $form->field($model, 'current_password')->passwordInput() ?>

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= \yii\helpers\Html::submitButton(Yii::t('user', 'Update email'), ['class' => 'btn btn-success']) ?><br>
                    </div>
                </div>

                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
