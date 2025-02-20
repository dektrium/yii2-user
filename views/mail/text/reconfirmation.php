<?php

/*
 * This file is part of the DDMTechDev project.
 *
 * (c) DDMTechDev project <http://github.com/ddmtechdev>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

/**
 * @var ddmtechdev\user\models\Token $token
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t(
    'user',
    'We have received a request to change the email address for your account on {0}',
    Yii::$app->name
) ?>.
<?= Yii::t('user', 'In order to complete your request, please click the link below') ?>.

<?= $token->url ?>

<?= Yii::t('user', 'If you cannot click the link, please try pasting the text into your browser') ?>.

<?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
