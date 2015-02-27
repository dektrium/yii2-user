<?php

use dektrium\user\models\Token;
use dektrium\user\models\User;
use tests\codeception\_pages\RegistrationPage;
use yii\helpers\Html;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that registration works');

$page = RegistrationPage::openBy($I);

$I->amGoingTo('try to register with empty credentials');
$page->register('', '', '');
$I->see('Username cannot be blank');
$I->see('Email cannot be blank');
$I->see('Password cannot be blank');

$I->amGoingTo('try to register with already used email and username');
$user = $I->getFixture('user')->getModel('user');
$page->register($user->username, $user->email, 'qwerty');
$I->see(Html::encode('This username has already been taken'));
$I->see(Html::encode('This email address has already been taken'));

$I->amGoingTo('try to register');
$page->register('tester', 'tester@example.com', 'tester');
$I->see('A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.');
$user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
$token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRMATION]);
$I->seeInEmail(Html::encode($token->url));
