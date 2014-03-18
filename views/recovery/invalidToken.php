<?php
use yii\helpers\Html;

/**
 * @var yii\base\View $this
 */
$this->title = Yii::t('user', 'Recovery token is invalid');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alert alert-danger">
    <h4><?= Yii::t('user', 'Recovery token is invalid') ?></h4>
    <?= Yii::t('user', 'We\'re sorry but your confirmation token is invalid. You can request new token by clicking the link below:') ?>
    <br>
    <?= Html::a(Yii::t('user', 'Password recovery'), ['/user/recovery/request']) ?>
</div>
