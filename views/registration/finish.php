<?php
use yii\helpers\Html;

/**
 * @var yii\base\View $this
 */
$this->title = 'Sign up';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-register">
    <div class="alert alert-success">
        <h4>Your account has been confirmed</h4>
        You can log in using your credentials <?= Html::a('here', ['/user/auth/login']); ?>
    </div>
</div>