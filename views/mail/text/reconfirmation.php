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
 * @var dektrium\user\models\Token $token
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'You have recently requested email change on {0}', Yii::$app->name) ?>.
<?= Yii::t('user', 'In order to complete your request, please click the link below') ?>.

<?= $token->url ?>

<?= Yii::t('user', 'If you have problems, please paste the above URL into your web browser') ?>.
<?= Yii::t('user', 'This URL will only be valid for a limited time and will expire') ?>.

<?= Yii::t('user', 'P.S. If you received this email by mistake, simply delete it') ?>.
