<?php

/**
 * @var yii\base\View $this
 */
$this->title = Yii::t('user', 'Your account has been confirmed');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-success">
    <h4><?= Yii::t('user', 'Your account has been confirmed') ?></h4>
    <?= Yii::t('user', 'You can log in using your credentials now') ?>.
</div>
