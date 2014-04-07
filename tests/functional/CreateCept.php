<?php

use dektrium\user\tests\_pages\CreatePage;
use dektrium\user\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that user creation works');

$loginPage = LoginPage::openBy($I);
$user = $I->getFixture('user')->getModel('user');
$loginPage->login($user->email, 'qwerty');

$page = CreatePage::openBy($I);

$I->amGoingTo('try to create user with empty fields');
$page->create('', '', '');
$I->expectTo('see validations errors');
$I->see('Username cannot be blank.');
$I->see('Email cannot be blank.');

$page->create('toster', 'toster@example.com', 'toster');
$I->see('User has been created');
$I->see('toster');
$I->see('toster@example.com');

Yii::$app->user->logout();
$I->grabRecord('dektrium\user\models\User', ['email' => 'toster@example.com'])->confirm();
LoginPage::openBy($I)->login('toster@example.com', 'toster');
$I->see('Logout');
