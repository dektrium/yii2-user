<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;

/**
 * @var dektrium\user\mail\RegistrationEmail $email
 */

?>
<?= Yii::t('user', 'Hello') ?>,
<?= Yii::t('user', 'Your account on {0} has been created', Yii::$app->name) ?>.
<?php if ($email->isPasswordShown()): ?>
    <?= Yii::t('user', 'We have generated a password for you') ?>:
    <?= $email->getUser()->password ?>
<?php endif ?>
<?php if ($email->getConfirmationLink()): ?>
    <?= Yii::t('user', 'In order to complete your registration, please click the link below') ?>.
    <?= $email->getConfirmationLink() ?>
    <?= Yii::t('user', 'If you cannot click the link, please try pasting the text into your browser') ?>.
<?php endif ?>
<?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
