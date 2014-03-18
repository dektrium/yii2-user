<?php

/**
 * @var string $password
 * @var dektrium\user\models\User $user
 */
?>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'Hi there,')?>
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'You recently registered for a new account on {sitename}', ['sitename' => Yii::$app->name]) ?>.
    <?= Yii::t('user', 'Your may log in with following credentials') ?>:<br>
    <?= Yii::t('user', 'Login') ?>: <?= $user->username ?><br>
    <?= Yii::t('user', 'Password') ?>: <?= $password ?>.
</p>
