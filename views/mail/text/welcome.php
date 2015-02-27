<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var dektrium\user\models\User $user
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'Your account on {0} has been created', Yii::$app->name) ?>.
<?= Yii::t('user', 'You can now log in with the following credentials:') ?>.

<?= Yii::t('user', 'Email') ?>: <?= $user->email ?>

<?= Yii::t('user', 'Username') ?>: <?= $user->username ?>

<?= Yii::t('user', 'Password') ?>: <?= $user->password ?>

<?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
