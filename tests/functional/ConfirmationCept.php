<?php

use yii\helpers\Url;
use dektrium\user\models\User;

$I = new TestGuy($scenario);
$I->wantTo('ensure that confirmation works');

$I->amGoingTo('check that error is showed when token expired');
$user = $I->getFixture('user')->getModel('unconfirmed_with_expired_token');
$I->amOnPage(Url::toRoute(['/user/registration/confirm', 'id' => $user->id, 'token' => $user->confirmation_token]));
$I->see('Confirmation token is invalid');

$I->amGoingTo('check that user get confirmed');
$user = $I->getFixture('user')->getModel('unconfirmed');
$I->amOnPage(Url::toRoute(['/user/registration/confirm', 'id' => $user->id, 'token' => $user->confirmation_token]));
$I->see('Your account has been confirmed');
$I->seeRecord(User::className(), [
    'id' => $user->id,
    'confirmation_token' => null,
    'confirmation_sent_at' => null
]);
