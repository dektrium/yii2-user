<?php

use dektrium\user\tests\_pages\RegisterPage;
use yii\helpers\Html;
use dektrium\user\models\User;

$I = new TestGuy($scenario);
$I->wantTo('ensure that registration works');

$page = RegisterPage::openBy($I);

$I->amGoingTo('try to register with empty credentials');
$page->register('', '', '');
$I->expectTo('see validations errors');
$I->see('Username cannot be blank.');
$I->see('Email cannot be blank.');
$I->see('Password cannot be blank.');

$I->amGoingTo('try to register with enabled confirmation');
$page->register('tester', 'tester@example.com', 'tester');
$I->see('Awesome, almost there! We need to confirm your email address');
$I->haveRecord('\dektrium\user\models\User', ['email' => 'tester@example.com', 'username' => 'tester']);

$I->expect('confirmation email has been sent');
$user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
$I->seeInEmail(Html::encode($user->getConfirmationUrl()));
