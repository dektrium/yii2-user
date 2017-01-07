<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\helpers\FeatureHelper;
use dektrium\user\models\User;
use dektrium\user\models\UserSearch;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\ToggleButtonGroup;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('/admin/_menu') ?>

<?php Pjax::begin() ?>

<?php $form = ActiveForm::begin([
    'method' => 'GET',
    'action' => Url::canonical(),
    'enableClientValidation' => false,
    'options' => ['data-pjax' => 1],
]) ?>

<?php if (FeatureHelper::isEmailConfirmationEnabled()): ?>
    <?= $form->field($searchModel, 'confirmStatus')->widget(ToggleButtonGroup::className(), [
        'type' => 'radio',
        'items' => $searchModel->getConfirmStatusList(),
        'labelOptions' => ['uncheck' => false, 'class' => 'btn-default'],
        'options' => [],
    ]) ?>
<?php endif ?>
<?= $form->field($searchModel, 'blockStatus')->widget(ToggleButtonGroup::className(), [
    'type' => 'radio',
    'items' => $searchModel->getBlockStatusList(),
    'labelOptions' => ['class' => 'btn-default'],
]) ?>
<?php if (FeatureHelper::isAdminApprovalEnabled()): ?>
    <?= $form->field($searchModel, 'approveStatus')->widget(ToggleButtonGroup::className(), [
        'type' => 'radio',
        'items' => $searchModel->getApproveStatusList(),
        'labelOptions' => ['class' => 'btn-default'],
    ]) ?>
<?php endif ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('user', 'Filter'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('user', 'Reset filter'), ['/user/admin/index'], ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end() ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => "{items}\n{pager}",
    'columns' => [
        [
            'attribute' => 'id',
            'options' => ['style' => 'width: 10%;'],
            'contentOptions' => ['style' => 'text-align: center'],
        ],
        [
            'attribute' => 'username',
            'format' => 'raw',
            'value' => function (User $model) {
                $content = Html::a(Html::encode($model->username), ['update', 'id' => $model->id]);
                if ($model->getIsBlocked()) {
                    $content .= ' <i class="glyphicon glyphicon-lock text-danger" title="'
                        . Yii::t('user', 'User is blocked')
                        . '"></i>';
                }
                if (FeatureHelper::isEmailConfirmationEnabled() && !$model->getIsConfirmed()) {
                    $content .= ' <i class="glyphicon glyphicon-envelope text-danger" title="'
                        . Yii::t('user', 'Email is not confirmed')
                        . '"></i>';
                }
                if (FeatureHelper::isAdminApprovalEnabled() && !$model->isApproved()) {
                    $content .= ' <i class="glyphicon glyphicon-remove text-danger" title="'
                        . Yii::t('user', 'User is not approved')
                        . '"></i>';
                }

                return $content;
            },
        ],
        [
            'attribute' => 'email',
            'format' => 'html',
            'value' => function (User $model) {
                return Html::a(Html::encode($model->email), ['update', 'id' => $model->id]);
            },
        ],
        [
            'attribute' => 'registration_ip',
            'value' => function ($model) {
                return $model->registration_ip == null
                    ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
                    : $model->registration_ip;
            },
            'format' => 'html',
        ],
        [
            'attribute' => 'created_at',
            'value' => function ($model) {
                if (extension_loaded('intl')) {
                    return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                } else {
                    return date('Y-m-d G:i:s', $model->created_at);
                }
            },
        ],
        [
            'header' => false,
            'format' => 'raw',
            'value' => function (User $model) {
                return ButtonDropdown::widget([
                    'label' => Yii::t('user', 'Actions'),
                    'options' => ['class' => 'btn-default btn-xs'],
                    'dropdown' => [
                        'items' => [
                            ['label' => Yii::t('user', 'Update'), 'url' => ['update', 'id' => $model->id]],
                            ['label' => Yii::t('user', 'Confirm email'), 'url' => ['confirm', 'id' => $model->id], 'linkOptions' => [
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to confirm email of this user?'),
                            ], 'visible' => FeatureHelper::isEmailConfirmationEnabled() && !$model->getIsConfirmed()],
                            ['label' => Yii::t('user', 'Approve'), 'url' => ['approve', 'id' => $model->id], 'linkOptions' => [
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to approve this user?'),
                            ], 'visible' => FeatureHelper::isAdminApprovalEnabled() && !$model->isApproved()],
                            ['label' => Yii::t('user', 'Block'), 'url' => ['block', 'id' => $model->id], 'linkOptions' => [
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                            ], 'visible' => !$model->getIsBlocked()],
                            ['label' => Yii::t('user', 'Unblock'), 'url' => ['block', 'id' => $model->id], 'linkOptions' => [
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                            ], 'visible' => $model->getIsBlocked()],
                            '<li role="presentation" class="divider"></li>',
                            ['label' => Yii::t('user', 'Delete'), 'url' => ['delete', 'id' => $model->id], 'linkOptions' => [
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to delete this user?'),
                            ]],
                        ],
                    ],
                ]);
            },
        ],
    ],
]); ?>

<?php Pjax::end() ?>
