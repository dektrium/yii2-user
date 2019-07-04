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
 * @var dektrium\user\models\Token[] $tokens
 */

use dektrium\user\helpers\RecoveryCodesHelper;
use dektrium\user\widgets\RecoveryCodes;
use yii\data\ArrayDataProvider;
use yii\di\Instance;


/** @var RecoveryCodesHelper $helper */
$helper = Instance::ensure(RecoveryCodesHelper::class);
?>


<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'Hello') ?>,
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'You have enabled two-factor authentication.') ?>.
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'Please save recovery codes.') ?>.
</p>
<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user',
        'Recovery codes are the only way to access your account again. By saving your recovery codes, you’ll be able to regain access if you:') ?>
</p>
<ul>
    <li><?= Yii::t('user', 'Lose your phone') ?></li>
    <li>
        <?= Yii::t('user', 'Delete your authenticator application') ?>
    </li>
</ul>

<p style="font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; font-weight: normal; margin: 0 0 10px; padding: 0;">
    <?= Yii::t('user', 'Recovery codes') ?>:
</p>
<div>
    <?= RecoveryCodes::widget([
        'itemView' => RecoveryCodes::HTML_VIEW,
        'dataProvider' => Instance::ensure([
            'class' => ArrayDataProvider::class,
            'allModels' => $helper->prepareDataHtmlView($codes),
        ])
    ]) ?>
</div>
