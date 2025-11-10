<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use AlexeiKaDev\Yii2User\widgets\Connect;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var \AlexeiKaDev\Yii2User\models\User $user The current user.
 */

$this->title = Yii::t('user', 'Networks');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <p><?= Yii::t('user', 'You can connect multiple accounts to be able to log in using them') ?>.</p>
                </div>
                <?php $auth = Connect::begin([
                    'baseAuthUrl' => ['/user/security/auth'],
                    'accounts' => $user->accounts,
                    'autoRender' => false,
                    'popupMode' => false,
                ]) ?>
                <table class="table">
                    <?php foreach ($auth->getClients() as $client): ?>
                        <tr>
                            <td style="width: 32px;" class="align-middle">
                                <?= Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]) ?>
                            </td>
                            <td class="align-middle">
                                <strong><?= $client->getTitle() ?></strong>
                            </td>
                            <td style="width: 120px">
                                <?= $auth->isConnected($client) ?
                                    Html::a(Yii::t('user', 'Disconnect'), $auth->createClientUrl($client), [
                                        'class' => 'btn btn-danger w-100',
                                        'data-method' => 'post',
                                    ]) :
                                    Html::a(Yii::t('user', 'Connect'), $auth->createClientUrl($client), [
                                        'class' => 'btn btn-success w-100',
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
