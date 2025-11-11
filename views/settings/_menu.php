<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use AlexeiKaDev\Yii2User\widgets\UserMenu;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <?= Html::img(Yii::$app->user->identity->profile->getAvatarUrl(24), [
                'class' => 'rounded',
                'alt' => Yii::$app->user->identity->username,
            ]) ?>
            <?= Yii::$app->user->identity->username ?>
        </h3>
    </div>
    <div class="card-body">
        <?= UserMenu::widget() ?>
    </div>
</div>
