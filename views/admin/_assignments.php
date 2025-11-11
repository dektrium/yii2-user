<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap5\Alert;
use yii\rbac\Item;
use yii\web\View;
use yii2developer\rbac\widgets\Assignments;

/**
 * @var View $this
 * @var AlexeiKaDev\Yii2User\models\User $user
 * @var array $assignments Array of Item objects
 * @var string[] $available
 * @var string[] $assigned
 */

$this->title = Yii::t('user', 'Assignments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@AlexeiKaDev/Yii2User/views/admin/update.php', ['user' => $user]) ?>

<?= Alert::widget([
    'options' => [
        'class' => 'alert-success alert-dismissible',
    ],
    'body' => Yii::$app->session->getFlash('success'),
]) ?>

<?= Assignments::widget(['userId' => $user->id]) ?>

<?php $this->endContent() ?>
