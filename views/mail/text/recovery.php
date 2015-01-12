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
 * @var dektrium\user\models\User  $user
 * @var dektrium\user\models\Token $token
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'You have recently requested to reset your password on {0}', Yii::$app->name) ?>.
<?= Yii::t('user', 'In order to complete your request, we need you to verify that you initiated this request') ?>.
<?= Yii::t('user', 'Please click the link below to complete your password reset') ?>.

<?= $token->url ?>

<?= Yii::t('user', 'P.S. If you did not request to reset your password, please disregard this message. Your account is safe') ?>.
