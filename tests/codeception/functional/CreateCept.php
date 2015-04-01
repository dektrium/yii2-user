<?php

use dektrium\user\tests\FunctionalTester;
use tests\codeception\_pages\CreatePage;
use tests\codeception\_pages\LoginPage;

$I = new FunctionalTester($scenario);
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
LoginPage::openBy($I)->login('toster@example.com', 'toster');
$I->see('Logout');
