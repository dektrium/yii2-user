<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var $this  yii\web\View
 * @var $form  yii\widgets\ActiveForm
 * @var $settings dektrium\user\models\SettingsForm
 * @var $account_deletion dektrium\user\models\AccountDeletionForm
 * @var $password_change dektrium\user\models\PasswordChangeForm
 */

$this->title = Yii::t('user', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;

$form_options = [
    'options' => ['class' => 'form-horizontal'],
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-sm-offset-4 col-lg-8\">{error}\n{hint}</div>",
        'labelOptions' => ['class' => 'col-lg-4 control-label']
    ],
];

?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>

    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
                    </div>
                    <div class="panel-body">
                        <?php $form = ActiveForm::begin(ArrayHelper::merge($form_options, ['id' => 'account-form-settings'])); ?>

                        <?= Html::hiddenInput('settings-form[scenario]', 'user_settings_change'); ?>

                        <?= $form->field($settings, 'email') ?>

                        <?= $form->field($settings, 'username') ?>

                        <div class="form-group">
                            <div class="col-lg-offset-6 col-lg-6">
                                <?= Html::submitButton(Yii::t('user', 'Save account settings'), ['class' => 'btn btn-block btn-success']) ?>
                                <br>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <?php $form = ActiveForm::begin(ArrayHelper::merge($form_options, ['id' => 'password-change-form'])); ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= Yii::t('user', 'Change password'); ?></h3>
                    </div>
                    <div class="panel-body">

                        <?= $form->field($password_change, 'new_password')->passwordInput() ?>

                        <?= $form->field($password_change, 'new_password_confirmation')->passwordInput() ?>

                        <hr/>

                        <?= $form->field($password_change, 'current_password')->passwordInput() ?>

                        <div class="form-group">
                            <div class="col-lg-offset-6 col-lg-6">
                                <?= Html::submitButton(Yii::t('user', 'Change password'), ['class' => 'btn btn-block btn-success']) ?>
                                <br>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>


                <?php if (Yii::$app->getModule('user')->enableAccountDelete): ?>
                    <div class="panel panel-danger">
                        <?php $form = ActiveForm::begin(ArrayHelper::merge($form_options, [
                            'id' => 'account-form-account-deletion',
                            'action' => Url::to(['delete']),
                        ])); ?>

                        <div class="panel-heading">
                            <h3 class="panel-title"><?= Yii::t('user', 'Delete account') ?></h3>
                        </div>
                        <div class="panel-body">
                            <p>
                                <?= Yii::t('user', 'Once you delete your account, there is no going back') ?>.
                                <?= Yii::t('user', 'It will be deleted forever') ?>.
                                <?= Yii::t('user', 'Please be certain') ?>.
                            </p>

                            <?= $form->field($account_deletion, 'current_password')->passwordInput() ?>

                            <?= Html::a(Yii::t('user', 'Delete account'), ['delete'], [
                                'class' => 'btn btn-danger pull-right',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure? There is no going back'),
                            ]) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                <?php endif ?>

            </div>
        </div>
    </div>
</div>

