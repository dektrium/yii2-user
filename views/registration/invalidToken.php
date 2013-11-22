<?php
use yii\helpers\Html;

/**
 * @var yii\base\View $this
 */
$this->title = 'Sign up';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-register">
    <div class="alert alert-danger">
        <h4>Confirmation token is invalid</h4>
        We're sorry but your confirmation token is invalid. You can request new token
        <?= Html::a('here', ['/user/registration/resend']);?>
    </div>
</div>
