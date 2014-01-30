<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var dektrium\user\models\UserSearch $searchModel
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

	<h1><?= Html::encode($this->title) ?> <?= Html::a(Yii::t('user', 'Create user'), ['create'], ['class' => 'btn btn-success']) ?></h1>

	<?php if (Yii::$app->getSession()->hasFlash('user_created')): ?>
		<div class="alert alert-success">
			<p>User has been successfully created</p>
		</div>
	<?php endif; ?>

	<?php if (Yii::$app->getSession()->hasFlash('user_deleted')): ?>
		<div class="alert alert-success">
			<p>User has been successfully deleted</p>
		</div>
	<?php endif; ?>

	<?php echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		'columns' => [
			'username',
			'email:email',
			[
				'attribute' => 'create_time',
				'label' => 'Registration time',
				'value' => function ($model, $index, $widget) {
					return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->create_time]);
				}
			],
			[
				'attribute' => 'registration_ip',
				'value' => function ($model, $index, $widget) {
					return $model->registration_ip == null ? '<span class="not-set">(not set)</span>' : long2ip($model->registration_ip);
				},
				'format' => 'html'
			],
			[
				'value' => function ($model, $index, $widget) {
					return $model->isConfirmed ? '<span class="text-success">Confirmed</span>' : '<span class="text-warning">Not confirmed</span>';
				},
				'format' => 'html'
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{confirm} {update} {delete}',
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
					'confirm' => function ($url, $model) {
						$params =  [
							'class' => 'btn btn-xs btn-success',
							'data-method' => 'post',
							'data-confirm' => 'Are you sure to confirm this user?',
							'title' => Yii::t('user', 'Confirm'),
						];
						if ($model->getIsConfirmed()) {
							$params['disabled'] = 'disabled';
						}
						return Html::a('<i class="glyphicon glyphicon-ok"></i>', $url, $params);
					}
				]
			],
		],
	]); ?>

</div>