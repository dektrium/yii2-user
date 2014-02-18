<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $model
 */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\dektrium\user\assets\Passfield::register($this);
$this->registerJs(sprintf('$("#user-password").passField({"locale": "%s", "length": {"min": 6, "max": 40 }});', Yii::$app->language));
?>
<div class="user-create">

	<h1><?= Html::encode($this->title) ?></h1>


	<div class="user-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'username')->textInput(['maxlength' => 25]) ?>

		<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>

		<?= $form->field($model, 'password')->passwordInput() ?>

		<div class="form-group">
			<?= Html::submitButton('Create', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>
