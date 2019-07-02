<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dektrium\user\models\SessionHistory;

/* @var $this yii\web\View */
/* @var $searchModel dektrium\user\models\SessionHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('user', 'Session history');
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
                <?= Html::a(
                    Yii::t('user', 'Terminate all sessions'),
                    ['/user/security/terminate-sessions'],
                    [
                        'class' => 'btn btn-danger btn-xs pull-right',
                        'data-method' => 'post'
                    ]
                ) ?>
            </div>
            <div class="panel-body">

                <?php Pjax::begin(); ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        'user_agent',
                        'ip',
                        [
                            'contentOptions' => [
                                'class' => 'text-nowrap',
                            ],
                            'label' => Yii::t('user', 'Status'),
                            'value' => function (SessionHistory $model) {
                                if ($model->getIsActive()) {
                                    if ($model->session_id === session_id()) {
                                        $value = Yii::t('user', 'Current');
                                    } else {
                                        $value = Yii::t('user', 'Active');
                                    }
                                } else {
                                    $value = Yii::t('user', 'Expired');
                                }

                                return $value;
                            },
                        ],
                        [
                            'attribute' => 'updated_at',
                            'format' => 'datetime'
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>

