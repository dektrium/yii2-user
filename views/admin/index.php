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
			'create_time:datetime',
			'registration_ip',
			[
				'value' => function ($model) {
					return $model->isConfirmed ? '<span class="text-success">Confirmed</span>' : '<span class="text-warning">Not confirmed</span>';
				},
				'format' => 'html'
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>

</div>