<?php

/**
 * @var Codeception\Scenario $scenario
 */

use tests\_fixtures\UserFixture;
use tests\_pages\CreatePage;
use tests\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that user creation works');
$I->haveFixtures(['user' => UserFixture::className()]);

$loginPage = LoginPage::openBy($I);
$user = $I->grabFixture('user', 'user');
$loginPage->login($user->email, 'qwerty');

$page = CreatePage::openBy($I);

$I->amGoingTo('try to create user with empty fields');
$page->create('', '', '');
$I->expectTo('see validations errors');
$I->see('Username cannot be blank.');
$I->see('Email cannot be blank.');

$page->create('foobar', 'foobar@example.com', 'foobar');
$I->see('User has been created');

Yii::$app->user->logout();
LoginPage::openBy($I)->login('foobar@example.com', 'foobar');
$I->see('Logout');
