<?php
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */
?>
<p>
    You recently registered for a new account with <?= Yii::$app->name; ?>. Before your account is activated, we
    need you to confirm your email address.
</p>
<p>
    To complete your registration, please click the link below:<br>
    <?= Html::a($user->getConfirmationUrl(), $user->getConfirmationUrl()); ?>
</p>
