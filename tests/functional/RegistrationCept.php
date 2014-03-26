<?php

use dektrium\user\tests\_pages\RegisterPage;
use yii\helpers\Html;
use dektrium\user\models\User;

$I = new TestGuy($scenario);
$I->wantTo('ensure that registration works');

$page = RegisterPage::openBy($I);

$I->amGoingTo('try to register with empty credentials');
$page->register('', '', '');
$I->see('Username cannot be blank');
$I->see('Email cannot be blank');
$I->see('Password cannot be blank');

$I->amGoingTo('try to register with already used email and username');
$page->register('user', 'user@example.com', 'qwerty');
$I->see(Html::encode('Username "user" has already been taken'));
$I->see(Html::encode('Email "user@example.com" has already been taken'));

$I->amGoingTo('try to register with enabled confirmation');
$page->register('tester', 'tester@example.com', 'tester');
$I->see('Awesome, almost there! We need to confirm your email address');
$user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
$I->seeInEmail(Html::encode($user->getConfirmationUrl()));
