<?php

$this->title = 'List of actions';
$actions = [
    ['Register', ['/user/registration/register'], 'Register a new user'],
    ['Resend', ['/user/registration/resend'], 'Resend confirmation token'],
    ['Confirm', ['/user/registration/confirm'], 'Confirm a user (needs id and token query params)'],
    ['Login', ['/user/auth/login'], 'Displays login form'],
    ['Logout', ['/user/auth/logout'], 'Logs the user out (POST only)'],
    ['Recovery', ['/user/recovery/request'], 'Request new recovery token'],
    ['Reset', ['/user/recovery/reset'], 'Reset password (needs id and token query params)'],
    ['Admin', ['/user/admin'], 'Administrator panel'],
];
?>
<div class="alert alert-info"><strong>Version info: Yii2-user <?= \dektrium\user\Module::VERSION ?></strong>
    <p>This page has a list of available actions for Yii2-user module</p></div>
<table class="table">
    <tr>
        <th>Action</th>
        <th>Description</th>
    </tr>
    <?php foreach ($actions as $action): ?>
        <tr>
            <td><?= \yii\helpers\Html::a($action[0], $action[1]) ?></td>
            <td><?= $action[2] ?></td>
        </tr>
    <?php endforeach ?>
</table>
