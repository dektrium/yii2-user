<?php

/**
 * @var $content
 */

$alertTypes = [
    'error'   => 'alert-danger',
    'danger'  => 'alert-danger',
    'success' => 'alert-success',
    'info'    => 'alert-info',
    'warning' => 'alert-warning'
];
$session = Yii::$app->session;
$flashes = $session->getAllFlashes();
foreach ($flashes as $type => $data) {
    if (isset($alertTypes[$type])) {
        $data = (array) $data;
        foreach ($data as $i => $message) {
            echo \yii\bootstrap\Alert::widget([
                'body' => $message,
                'options' => ['class' => $alertTypes[$type]],
            ]);
        }
        $session->removeFlash($type);
    }
}

if (Yii::$app->user->getIsGuest()) {
    echo \yii\helpers\Html::a('Login', ['/user/security/login']);
    echo \yii\helpers\Html::a('Registration', ['/user/registration/register']);
} else {
    echo \yii\helpers\Html::a('Logout', ['/user/security/logout']);
}

echo $content;
