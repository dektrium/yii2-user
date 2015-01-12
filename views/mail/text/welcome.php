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

<?= Yii::t('user', 'Your account on {0} has been successfully created and we have generated password for you', Yii::$app->name) ?>.
<?= Yii::t('user', 'You can use it with your email address or username in order to log in') ?>.

<?= Yii::t('user', 'Email') ?>: <?= $user->email ?>

<?= Yii::t('user', 'Username') ?>: <?= $user->username ?>

<?= Yii::t('user', 'Password') ?>: <?= $user->password ?>

<?= Yii::t('user', 'P.S. If you received this email by mistake, simply delete it') ?>.
