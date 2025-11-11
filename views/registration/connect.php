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
 * @var yii\web\View $this
 * @var AlexeiKaDev\Yii2User\models\User $model
 * @var AlexeiKaDev\Yii2User\models\Account $account
 */

$this->title = Yii::t('user', 'Connect your account');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row justify-content-center">
    <div class="col-md-4 col-sm-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <p>
                        <?= Yii::t(
                            'user',
                            'In order to finish your registration, we need you to enter following fields'
                        ) ?>:
                    </p>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'connect-account-form',
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'username') ?>

                <?= Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-success w-100']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <p class="text-center mt-3">
            <?= Html::a(
                Yii::t(
                    'user',
                    'If you already registered, sign in and connect this account on settings page'
                ),
                ['/user/settings/networks']
            ) ?>.
        </p>
    </div>
</div>
