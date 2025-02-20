<?php

/*
 * This file is part of the DDMTechDev project
 *
 * (c) DDMTechDev project <http://github.com/ddmtechdev>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use ddmtechdev\rbac\widgets\Assignments;

/**
 * @var yii\web\View $this
 * @var ddmtechdev\user\models\User $user
 */
?>

<?php $this->beginContent('@ddmtechdev/user/views/admin/update.php', ['user' => $user]) ?>

<?= yii\bootstrap\Alert::widget([
    'options' => [
        'class' => 'alert-info alert-dismissible',
    ],
    'body' => Yii::t('user', 'You can assign multiple roles or permissions to user by using the form below'),
]) ?>

<?= Assignments::widget(['userId' => $user->id]) ?>

<?php $this->endContent() ?>
