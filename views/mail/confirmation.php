<?php
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */

$url = Yii::$app->getUrlManager()->createAbsoluteUrl('/user/confirmation/confirm', [
	'id' => $user->id,
	'token' => $user->confirmation_token
]);
?>
<p>
	You recently registered for a new account with <?= Yii::$app->name; ?> Before your account is activated, we
	need you to confirm your email address.
</p>
<p>
	To complete your registration, please click the link below:
	<?= Html::a($url, $url); ?>
</p>
