<?php

use dektrium\user\widgets\RecoveryCodes;
use yii\data\ArrayDataProvider;
use yii\di\Instance;
use dektrium\user\helpers\RecoveryCodesHelper;
use yii\bootstrap\Html;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\Token[] $codes
 */

/** @var RecoveryCodesHelper $helper */
$helper = Instance::ensure(RecoveryCodesHelper::class);
?>
<div class="row">
    <div class="col-xs-12">
        <?= Yii::t(
            'user',
            'Recovery codes are used to access your account in the event you cannot receive two-factor authentication codes'
        ) ?>.
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= RecoveryCodes::widget([
            'dataProvider' => Instance::ensure([
                'class' => ArrayDataProvider::class,
                'allModels' => $helper->prepareDataHtmlView($codes),
            ])
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= Html::a(
            Yii::t('user', 'Regenerate'), ['/user/settings/two-factor-regenerate-recovery-codes'], [
                'data' => [
                    'pjax' => 1,
                    'method' => 'post'
                ],
                'class' => 'btn btn-warning pull-right'
            ]
        ) ?>
    </div>
</div>
