<?php

if (Yii::$app->user->getIsGuest()) {
    echo \yii\helpers\Html::a('Login', ['/user/auth/login']);
    echo \yii\helpers\Html::a('Registration', ['/user/registration/register']);
} else {
    echo \yii\helpers\Html::a('Logout', ['/user/auth/logout']);
}

echo $content;