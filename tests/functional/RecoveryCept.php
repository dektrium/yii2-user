<?php

use dektrium\user\tests\_pages\RecoveryPage;
use dektrium\user\tests\_pages\LoginPage;
use yii\helpers\Html;
use dektrium\user\models\User;

$I = new TestGuy($scenario);
$I->wantTo('ensure that password recovery works');

$page = RecoveryPage::openBy($I);

$I->amGoingTo('try to request recovery token for unconfirmed account');
$page->resend('unconfirmed@example.com');
$I->see('You must confirm your account first');

$I->amGoingTo('try to request recovery token');
$page->resend('user@example.com');
$I->see('You have been sent an email with instructions on how to reset your password.');
$I->seeEmailIsSent();
$email = $I->getLastMessage();
$user = $I->grabRecord(User::className(), ['email' => 'user@example.com']);
$I->seeEmailHtmlContains(Html::encode($user->getRecoveryUrl()), $email);

$I->amGoingTo('reset password');
$I->amOnPage('/?r=user/recovery/reset&id=5&token=dghFKJA6JvjTKLAwyE5w2XD9b2lmBXLE');
$I->see('Recovery token is invalid');

$I->amOnPage('/?r=user/recovery/reset&id=' . $user->id . '&token=' . $user->recovery_token);
$I->fillField('#recovery-form-password', 'newpass');
$I->click('Reset password');
$I->see('Your password has been reset');

$page = LoginPage::openBy($I);
$page->login('user@example.com', 'qwerty');
$I->see('Invalid login or password');
$page->login('user@example.com', 'newpass');
$I->dontSee('Invalid login or password');
