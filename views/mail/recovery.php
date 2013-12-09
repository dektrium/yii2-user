<?php
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */
?>
<p>
    You've recently requested to reset your password on <?= Yii::$app->name; ?>.

    In order to complete this request, we need you to verify that you initiated this request. Please click the link
    below to complete your password reset.
</p>
<p>
    <?= Html::a($user->getRecoveryUrl(), $user->getRecoveryUrl()); ?>
</p>
<p>
    P.S. If you did not request to reset your password, please disregard this message. Your account is safe.
</p>