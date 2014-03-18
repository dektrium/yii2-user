<?php

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

<h1><?= Html::encode($this->title) ?> <?= Html::a(Yii::t('user', 'Create user'), ['create'], ['class' => 'btn btn-success']) ?></h1>

<?php if (Yii::$app->getSession()->hasFlash('admin_user')): ?>
    <div class="alert alert-success">
        <p><?= Yii::$app->getSession()->getFlash('admin_user') ?></p>
    </div>
<?php endif; ?>

<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'layout' => "{items}\n{pager}",
    'columns' => [
        'username',
        'email:email',
        [
            'attribute' => 'registered_from',
            'value' => function ($model, $index, $widget) {
                    return $model->registered_from == null ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>' : long2ip($model->registered_from);
                },
            'format' => 'html',
            'visible' => Yii::$app->getModule('user')->trackable
        ],
        [
            'attribute' => 'created_at',
            'value' => function ($model, $index, $widget) {
                return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
            }
        ],
        [
            'header' => Yii::t('user', 'Confirmation'),
            'value' => function ($model, $index, $widget) {
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
            'visible' => Yii::$app->getModule('user')->confirmable
        ],
        [
            'header' => Yii::t('user', 'Block status'),
            'value' => function ($model, $index, $widget) {
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
                        'data-confirm' => 'Are you sure to delete this user?',
                        'title' => Yii::t('yii', 'Delete'),
                    ]);
                },
            ]
        ],
    ],
]); ?>
