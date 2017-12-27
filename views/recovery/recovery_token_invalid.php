<?php use yii\helpers\Html; ?>

<?= \Yii::t('user', 'Invalid or expired link'); ?>

<p> <?= Html::a(Yii::t('user', 'Back to Login'), ['//user/security/login'], ['class' => 'btn btn-primary']); ?> </p>

