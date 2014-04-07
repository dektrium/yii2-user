<?php

use dektrium\user\tests\_pages\ResendPage;
use dektrium\user\models\User;

$I = new TestGuy($scenario);
$I->wantTo('ensure that resending of confirmation tokens works');

$page = ResendPage::openBy($I);

$I->amGoingTo('try to resend token to non-existent user');
$page->resend('foo@example.com');
$I->see('Email is invalid');

$I->amGoingTo('try to resend token to already confirmed user');
$user = $I->getFixture('user')->getModel('user');
$page->resend($user->email);
$I->see('This account has already been confirmed');

$I->amGoingTo('try to resend token to unconfirmed user');
$user = $I->getFixture('user')->getModel('unconfirmed');
$I->seeRecord(User::className(), ['confirmation_token' => $user->confirmation_token]);
$page->resend($user->email);
$I->see('Awesome, almost there! We need to confirm your email address');
$I->dontSeeRecord(User::className(), ['confirmation_token' => $user->confirmation_token]);
