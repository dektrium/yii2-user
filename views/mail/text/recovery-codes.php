<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\widgets\RecoveryCodes;
use yii\data\ArrayDataProvider;
use yii\di\Instance;

/**
 * @var dektrium\user\models\User $user
 * @var dektrium\user\models\Token[] $tokens
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'You have enabled two-factor authentication') ?>.

<?= Yii::t('user', 'Please save recovery codes') ?>.
<?= Yii::t(
    'user',
    'Recovery codes are the only way to access your account again. By saving your recovery codes, you’ll be able to regain access if you:'
) ?>
    * <?= Yii::t('user', 'Lose your phone') ?></li>
    * <?= Yii::t('user', 'Delete your authenticator application') ?>

<?= Yii::t('user', 'Recovery codes') ?>:
<?= RecoveryCodes::widget([
    'itemView' => RecoveryCodes::TEXT_VIEW,
    'dataProvider' => Instance::ensure([
        'class' => ArrayDataProvider::class,
        'allModels' => $codes,
    ])
]) ?>