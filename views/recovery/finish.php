<?php

/**
 * @var yii\base\View $this
 */
$this->title = Yii::t('user', 'Password has been reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-success">
    <h4><?= Yii::t('user', 'Awesome! Your password has been reset') ?>.</h4>
    <?= Yii::t('user', 'You can log in using your new password now') ?>.
</div>
