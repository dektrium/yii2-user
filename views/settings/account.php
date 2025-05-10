<?php
declare(strict_types=1);

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
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var AlexeiKaDev\Yii2User\models\SettingsForm $model
 */

$this->title = Yii::t('user', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;

// @link https://github.com/yiisoft/yii2-bootstrap4/issues/169
if (isset($this->assetBundles['yii\\bootstrap4\\BootstrapAsset'])) {
    unset($this->assetBundles['yii\\bootstrap4\\BootstrapAsset']);
}

?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'account-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'new_password')->passwordInput() ?>

                <hr/>

                <?= $form->field($model, 'current_password')->passwordInput() ?>

                <div class="mt-3">
                    <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-success w-100']) ?><br>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <?php if ($model->module->enableAccountDelete): ?>
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title"><?= Yii::t('user', 'Delete account') ?></h3>
                </div>
                <div class="card-body">
                    <p>
                        <?= Yii::t('user', 'Once you delete your account, there is no going back') ?>.
                        <?= Yii::t('user', 'It will be deleted forever') ?>.
                        <?= Yii::t('user', 'Please be certain') ?>.
                    </p>
                    <?= Html::a(Yii::t('user', 'Delete account'), ['delete'], [
                        'class' => 'btn btn-danger',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure? There is no going back'),
                    ]) ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>
