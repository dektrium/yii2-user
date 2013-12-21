<?php

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */
?>
<p>
	You recently registered for a new account with <?= Yii::$app->name; ?>. Your credentials:<br>
	Username: <?= $user->username ?><br>
	Password: <?= $password ?>.
</p>