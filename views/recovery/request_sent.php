<?php use yii\helpers\Html; ?>

<p> <?= \Yii::t('user', 'An email has been sent with instructions for resetting your password'); ?>. </p>

<p> <?= \Yii::t('user', 'Please follow the instructions to change your password'); ?>. </p>

<p> <?= Html::a(Yii::t('user', 'Back to Login'), ['//user/security/login'], ['class' => 'btn btn-primary']); ?> </p>
