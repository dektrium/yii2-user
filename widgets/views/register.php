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
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $model
 * @var dektrium\user\Module $module
 */

?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
<?php if ($panel): ?>
        <div class="panel panel-<?= $panelType ?>">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($title) ?></h3>
            </div>
            <div class="panel-body">
<?php elseif ($title) : ?>
                <h3><?= Html::encode($title) ?></h3>
<?php endif; ?>
                <?php $form = ActiveForm::begin([
                    'id' => $id,
                    'action' => Url::to(['/user/register']),
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'username') ?>

                <?php if ($enableGeneratingPassword == false): ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                <?php endif ?>

                <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>

                <?php ActiveForm::end(); ?>
<?php if ($panel): ?>
            </div>
        </div>
<?php endif; ?>
<?php if ($displayLoginLink) : ?>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
        </p>
<?php endif; ?>
    </div>
</div>