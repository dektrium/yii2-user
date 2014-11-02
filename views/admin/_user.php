<?php

/**
 * @var yii\widgets\ActiveForm    $form
 * @var dektrium\user\models\User $user
 */

?>

<?= $form->field($user, 'username')->textInput(['maxlength' => 25]) ?>
<?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($user, 'password')->passwordInput() ?>
