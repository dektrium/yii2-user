<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $model
 */

$this->title = 'Update User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\dektrium\user\assets\Passfield::register($this);
$this->registerJs(sprintf('$("#user-password").passField({"locale": "%s", "length": {"min": 6, "max": 40 }});', Yii::$app->language));
?>
<div class="user-update">

	<h1><i class="glyphicon glyphicon-user"></i> <?= Html::encode($model->username) ?>
		<?php if (!$model->getIsConfirmed()): ?>
			<?= Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], ['class' => 'btn btn-success', 'data-method' => 'post']) ?>
		<?php endif; ?>
		<?php if (!is_null($model->recovery_token)): ?>
			<?= Html::a(Yii::t('user', 'Delete recovery tokens'), ['delete-tokens', 'id' => $model->id], ['class' => 'btn btn-warning', 'data-method' => 'post']) ?>
		<?php endif; ?>
		<?= Html::a(Yii::t('user', 'Delete user'), ['delete', 'id' => $model->id], ['class' => 'btn btn-danger', 'data-method' => 'post', 'data-confirm' => 'Are you sure to delete this user?']) ?>
	</h1>

	<?php if (Yii::$app->getSession()->hasFlash('user_confirmed')): ?>
		<div class="alert alert-success">
			<p>User has been successfully confirmed!</p>
		</div>
	<?php endif; ?>

	<?php if (Yii::$app->getSession()->hasFlash('tokens_deleted')): ?>
		<div class="alert alert-success">
			<p>Recovery tokens have been deleted!</p>
		</div>
	<?php endif; ?>

	<div class="user-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'username')->textInput(['maxlength' => 25]) ?>

		<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'password')->passwordInput() ?>

		<?php if (Yii::$app->getModule('user')->trackable): ?>
			<div class="panel panel-default">
				<div class="panel-heading">Trackable info</div>
				<div class="panel-body">
					<?= Yii::t('user', 'Registered at {0, date, MMMM dd, YYYY HH:mm} from {1}', [$model->create_time, is_null($model->registration_ip) ? 'N/D' : long2ip($model->registration_ip)]) ?>
					<br/>
					<?php if (!is_null($model->login_time)): ?>
						<?= Yii::t('user', 'Last login at {0, date, MMMM dd, YYYY HH:mm} from {1}', [$model->login_time, long2ip($model->login_ip)]) ?>
					<?php else: ?>
						<?= Yii::t('user', 'User has not logged in yet') ?>
					<?php endif;?>
				</div>
			</div>
		<?php endif;?>

		<div class="form-group">
			<?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>
