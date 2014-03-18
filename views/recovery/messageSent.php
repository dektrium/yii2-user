<?php

/**
 * @var yii\base\View $this
 * @var dektrium\user\forms\Recovery $model
 */
$this->title = Yii::t('user', 'Recovery message sent');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-info">
    <h4><?= Yii::t('user', 'Awesome, almost there!') ?></h4>
    <?= Yii::t('user', 'You have been sent an email with instructions on how to reset your password.') ?>
</div>
