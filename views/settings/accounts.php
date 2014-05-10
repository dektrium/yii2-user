<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\widgets\Connect;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */

$this->title = Yii::t('user', 'Networks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <?php if (Yii::$app->getSession()->hasFlash('account_connected')): ?>
        <div class="col-md-12">
            <div class="alert alert-success">
                <?= Yii::$app->getSession()->getFlash('account_connected') ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->getSession()->hasFlash('account_not_connected')): ?>
        <div class="col-md-12">
            <div class="alert alert-danger">
                <?= Yii::$app->getSession()->getFlash('account_not_connected') ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::$app->getUser()->getIdentity()->username ?></h3>
            </div>
            <div class="panel-body">
                <?= \yii\widgets\Menu::widget([
                    'options' => [
                        'class' => 'nav nav-pills nav-stacked'
                    ],
                    'items' => [
                        ['label' => Yii::t('user', 'Profile'), 'url' => ['/user/settings/profile']],
                        ['label' => Yii::t('user', 'Email'), 'url' => ['/user/settings/email']],
                        ['label' => Yii::t('user', 'Password'), 'url' => ['/user/settings/password']],
                        ['label' => Yii::t('user', 'Networks'), 'url' => ['/user/settings/networks']],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <?php $auth = Connect::begin([
                    'baseAuthUrl' => ['/user/settings/connect'],
                    'accounts'    => $user->connectedAccounts,
                    'autoRender'  => false,
                    'popupMode'   => false
                ]) ?>
                <table class="table">
                    <?php foreach ($auth->getClients() as $client): ?>
                        <tr>
                            <td style="width: 32px">
                                <?= Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]) ?>
                            </td>
                            <td>
                                <?= $client->getTitle() ?>
                            </td>
                            <td style="width: 120px">
                                <?= $auth->isConnected($client) ?
                                    Html::a(Yii::t('user', 'Disconnect'), $auth->createClientUrl($client), [
                                        'class' => 'btn btn-danger btn-block',
                                        'data-method' => 'post',
                                    ]) :
                                    Html::a(Yii::t('user', 'Connect'), $auth->createClientUrl($client), [
                                        'class' => 'btn btn-success btn-block'
                                    ])
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php Connect::end() ?>
            </div>
        </div>
    </div>
</div>
