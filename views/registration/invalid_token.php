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

?>
<div class="alert alert-danger">
    <h4>
        <?= Html::encode($this->title) ?>
    </h4>
    <p>
        <?= Yii::t('user', 'We are sorry but your confirmation token is out of date') ?>.
        <?= Yii::t('user', 'You can try requesting a new one by clicking the link below') ?>:
    </p>
    <p>
        <?= Html::a(Yii::t('user', 'Request new confirmation message'), ['/user/registration/resend']) ?>
    </p>
</div>
