<?php

/**
 * @var Codeception\Scenario $scenario
 */

use tests\_fixtures\UserFixture;
use tests\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that login works');
$I->haveFixtures(['user' => UserFixture::className()]);

$page = LoginPage::openBy($I);

$I->amGoingTo('try to login with empty credentials');
$page->login('', '');
$I->expectTo('see validations errors');
$I->see('Login cannot be blank.');
$I->see('Password cannot be blank.');

$I->amGoingTo('try to login with unconfirmed account');
$user = $I->grabFixture('user', 'unconfirmed');
$page->login($user->email, 'qwerty');
$I->see('You need to confirm your email address');

$I->amGoingTo('try to login with blocked account');
$user = $I->grabFixture('user', 'blocked');
$page->login($user->email, 'qwerty');
$I->see('Your account has been blocked');

$I->amGoingTo('try to login with wrong credentials');
$user = $I->grabFixture('user', 'user');
$page->login($user->email, 'wrong');
$I->expectTo('see validations errors');
$I->see('Invalid login or password');

$I->amGoingTo('try to login with correct credentials');
$page->login($user->email, 'qwerty');
$I->dontSee('Login');
$I->see('Logout');
