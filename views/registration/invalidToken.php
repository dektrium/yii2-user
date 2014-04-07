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

$this->title = Yii::t('user', 'Confirmation token is invalid');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-danger">
    <h4><?= Html::encode($this->title) ?></h4>
    <?= Yii::t('user', 'We are sorry but your confirmation token is invalid. Maybe it is out-of-date or does not exist. You can try requesting a new one by clicking the link below:') ?>
    <br>
    <?= Html::a(Yii::t('user', 'Request new confirmation message'), ['/user/registration/resend']) ?>
</div>
