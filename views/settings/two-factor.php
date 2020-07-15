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
use yii\bootstrap\Modal;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\TwoFactorEditForm $model
 */

$this->title = Yii::t('user', 'Two factor authentication');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
                <?php if ($model->isEnabled): ?>
                    <span class="label label-success"><?= Yii::t('user', 'Enabled'); ?></span>
                <?php else: ?>
                    <span class="label label-danger"><?= Yii::t('user', 'Disabled'); ?></span>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'options' => ['class' => 'form-horizontal'],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?php if ($model->isEnabled): ?>
                    <?= Html::activeHiddenInput($model, 'disable', [
                        'value' => true,
                    ]) ?>
                    <div class="col-lg-8 col-lg-offset-2">
                        <?= $form->field($model, 'secret')->textInput([
                            'readonly' => true,
                        ])->label(false) ?>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <?= Html::img($model->getQrCodeUrl(), [
                                    'class' => 'center-block',
                                ]) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-5">
                                <?= Html::submitButton(
                                    Yii::t('user', 'Disable'),
                                    ['class' => 'btn btn-block btn-danger']
                                ) ?>
                            </div>
                            <div class="col-lg-7">
                                <?php Modal::begin([
                                    'header' => Yii::t('user', 'Recovery codes'),
                                    'toggleButton' => [
                                        'label' => Yii::t('user', 'Recovery codes'),
                                        'class' => 'btn btn-block btn-primary',
                                    ],
                                ]); ?>
                                <?= $this->render('_recovery-codes', [
                                    'codes' => $model->getRecoveryCodes(),
                                ]) ?>

                                <?php Modal::end(); ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-lg-8 col-lg-offset-2">
                        <ol>
                            <li>
                                <?= Yii::t(
                                    'user',
                                    'Install {0} or another two-factor authentication application (TOTP, 2FA)',
                                    [
                                        Html::a(Yii::t('user', 'Google Authenticator'))
                                    ]
                                ) ?>
                            </li>
                            <li>
                                <?= Yii::t('user', 'Enter the secret code in the application or scan the QR code'); ?>
                                <?= Html::activeTextInput($model, 'secret', [
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]) ?>
                                <?= Html::img($model->getQrCodeUrl(), [
                                    'class' => 'center-block',
                                ]) ?>
                            </li>
                            <li>
                                <?= Yii::t('user', 'Enter the code from the application to activate') ?>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <?= Html::activeTextInput($model, 'code',
                                            [
                                                'class' => 'form-control',
                                            ]) ?>
                                        <?= Html::error($model, 'code') ?>
                                    </div>
                                </div>

                                <?= Html::submitButton(
                                    Yii::t('user', 'Enable'),
                                    ['class' => 'btn btn-block btn-success']
                                ) ?>
                            </li>
                        </ol>
                    </div>
                <?php endif; ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>
