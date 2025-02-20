<?php

/*
 * This file is part of the DDMTechDev project.
 *
 * (c) DDMTechDev project <http://github.com/ddmtechdev>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use ddmtechdev\user\widgets\UserMenu;

/**
 * @var ddmtechdev\user\models\User $user
 */

$user = Yii::$app->user->identity;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?= Html::img($user->profile->getAvatarUrl(24), [
                'class' => 'img-rounded',
                'alt' => $user->username,
            ]) ?>
            <?= $user->username ?>
        </h3>
    </div>
    <div class="panel-body">
        <?= UserMenu::widget() ?>
    </div>
</div>
