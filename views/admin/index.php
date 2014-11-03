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
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var dektrium\user\models\UserSearch $searchModel
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?> <?= Html::a(Yii::t('user', 'Create a user account'), ['create'], ['class' => 'btn btn-success']) ?></h1>

<?php echo $this->render('flash') ?>

<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout' => "{items}\n{pager}",
    'columns' => [
        'username',
        'email:email',
        [
            'attribute' => 'registration_ip',
            'value' => function ($model, $key, $index, $widget) {
                    return $model->registration_ip == null ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>' : long2ip($model->registration_ip);
                },
            'format' => 'html',
        ],
        [
            'attribute' => 'created_at',
            'value' => function ($model, $key, $index, $widget) {
                return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
            }
        ],
        [
            'header' => Yii::t('user', 'Confirmation'),
            'value' => function ($model, $key, $index, $widget) {
                if ($model->isConfirmed) {
                    return '<div class="text-center"><span class="text-success">' . Yii::t('user', 'Confirmed') . '</span></div>';
                } else {
                    return Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
                        'class' => 'btn btn-xs btn-success btn-block',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure to confirm this user?'),
                    ]);
                }
            },
            'format' => 'raw',
            'visible' => Yii::$app->getModule('user')->enableConfirmation
        ],
        [
            'header' => Yii::t('user', 'Block status'),
            'value' => function ($model, $key, $index, $widget) {
                if ($model->isBlocked) {
                    return Html::a(Yii::t('user', 'Unblock'), ['block', 'id' => $model->id], [
                        'class' => 'btn btn-xs btn-success btn-block',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure to unblock this user?')
                    ]);
                } else {
                    return Html::a(Yii::t('user', 'Block'), ['block', 'id' => $model->id], [
                        'class' => 'btn btn-xs btn-danger btn-block',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure to block this user?')
                    ]);
                }
            },
            'format' => 'raw',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('<i class="glyphicon glyphicon-wrench"></i>', $url, [
                        'class' => 'btn btn-xs btn-info',
                        'title' => Yii::t('yii', 'Update'),
                    ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                        'class' => 'btn btn-xs btn-danger',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure to delete this user?'),
                        'title' => Yii::t('yii', 'Delete'),
                    ]);
                },
            ]
        ],
    ],
]); ?>
