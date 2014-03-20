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

$this->title = Yii::t('user', 'Your account has been confirmed');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-success">
    <h4><?= Html::encode($this->title) ?></h4>
    <?= Yii::t('user', 'Awesome! You have successfully confirmed your email address. You may sign in using your credentials now.') ?>
</div>
