<?php

use dektrium\user\tests\_pages\RecoveryPage;
use dektrium\user\tests\_pages\LoginPage;
use yii\helpers\Html;
use yii\helpers\Url;
use dektrium\user\models\User;

$I = new TestGuy($scenario);
$I->wantTo('ensure that password recovery works');

$page = RecoveryPage::openBy($I);

$I->amGoingTo('try to request recovery token for unconfirmed account');
$user = $I->getFixture('user')->getModel('unconfirmed');
$page->recover($user->email);
$I->see('You need to confirm your email address');

$I->amGoingTo('try to request recovery token');
$user = $I->getFixture('user')->getModel('user');
$page->recover($user->email);
$I->see('You have been sent an email with instructions on how to reset your password.');
$user = $I->grabRecord(User::className(), ['email' => $user->email]);
$I->seeInEmail(Html::encode($user->getRecoveryUrl()));
$I->seeInEmailRecipients($user->email);

$I->amGoingTo('reset password with invalid token');
$user = $I->getFixture('user')->getModel('user_with_expired_recovery_token');
$I->amOnPage(Url::toRoute(['/user/recovery/reset', 'id' => $user->id, 'token' => $user->recovery_token]));
$I->see('Recovery token is invalid');

$I->amGoingTo('reset password');
$user = $I->getFixture('user')->getModel('user_with_recovery_token');
$I->amOnPage(Url::toRoute(['/user/recovery/reset', 'id' => $user->id, 'token' => $user->recovery_token]));
$I->fillField('#recovery-form-password', 'newpass');
$I->click('Finish');
$I->see('Password recovery finished');

$page = LoginPage::openBy($I);
$page->login($user->email, 'qwerty');
$I->see('Invalid login or password');
$page->login($user->email, 'newpass');
$I->dontSee('Invalid login or password');
