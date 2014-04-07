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
 * @var yii\web\View $this
 */

$this->title = Yii::t('user', 'Recovery message sent');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-info">
    <h4><?= Html::encode($this->title) ?></h4>
    <p>
        <?= Yii::t('user', 'You have been sent an email with instructions on how to reset your password.') ?>
        <?= Yii::t('user', 'Please check your email and click the reset link.') ?>
    </p>
    <p>
        <?= Yii::t('user', 'The email can take a few minutes to arrive. But if you are having troubles, you can request a new one.') ?>
    </p>
</div>
