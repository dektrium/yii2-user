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

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var AlexeiKaDev\Yii2User\models\UserSearch $searchModel
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<?= $this->render('/admin/_menu') ?>

<?php Pjax::begin() ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => "{items}\n{pager}",
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:90px;'], # 90px is sufficient for 5-digit user ids
        ],
        'username',
        'email:email',
        [
            'attribute' => 'registration_ip',
            'value' => fn ($model) => $model->registration_ip == null
                    ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
                    : $model->registration_ip,
            'format' => 'html',
        ],
        [
            'attribute' => 'created_at',
            'value' => fn ($model) => extension_loaded('intl')
                ? Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at])
                : date('Y-m-d G:i:s', $model->created_at),
        ],
        [
          'attribute' => 'last_login_at',
          'value' => fn ($model) => (!$model->last_login_at || $model->last_login_at == 0)
            ? Yii::t('user', 'Never')
            : (extension_loaded('intl')
                ? Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->last_login_at])
                : date('Y-m-d G:i:s', $model->last_login_at)),
        ],
        [
            'header' => Yii::t('user', 'Confirmation'),
            'value' => fn ($model) => $model->isConfirmed
                ? '<div class="text-center"><span class="text-success">' . Yii::t('user', 'Confirmed') . '</span></div>'
                : Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-success w-100',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                ]),
            'format' => 'raw',
            'visible' => Yii::$app->getModule('user')->enableConfirmation,
        ],
        [
            'header' => Yii::t('user', 'Block status'),
            'value' => fn ($model) => $model->isBlocked
                ? Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-success w-100',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                ])
                : Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-danger w-100',
                    'data-method' => 'post',
                    'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                ]),
            'format' => 'raw',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{switch} {resend_password} {update} {delete}',
            'buttons' => [
                'resend_password' => fn ($url, $model, $key) => (Yii::$app->user->identity->isAdmin && !$model->isAdmin)
                    ? '<a data-method="POST" data-confirm="' . Yii::t('user', 'Are you sure?') . '" href="' . Url::to(['resend-password', 'id' => $model->id]) . '" title="' . Yii::t('user', 'Generate and send new password to user') . '">[Resend Password]</a>'
                    : '',
                'switch' => fn ($url, $model) => (Yii::$app->user->identity->isAdmin && $model->id != Yii::$app->user->id && Yii::$app->getModule('user')->enableImpersonateUser)
                    ? Html::a('[Switch User]', ['/user/admin/switch', 'id' => $model->id], [
                        'title' => Yii::t('user', 'Become this user'),
                        'data-confirm' => Yii::t('user', 'Are you sure you want to switch to this user for the rest of this Session?'),
                        'data-method' => 'POST',
                    ])
                    : '',
            ]
        ],
    ],
]); ?>

<?php Pjax::end() ?>
