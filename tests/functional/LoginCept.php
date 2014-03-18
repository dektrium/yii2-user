<?php

use dektrium\user\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that login works');

$page = LoginPage::openBy($I);

$I->amGoingTo('try to login with empty credentials');
$page->login('', '');
$I->expectTo('see validations errors');
$I->see('Email cannot be blank.');
$I->see('Password cannot be blank.');

$I->amGoingTo('try to login with wrong credentials');
$page->login('user@example.com', 'wrong');
$I->expectTo('see validations errors');
$I->see('Invalid login or password');

$I->amGoingTo('try to login with unconfirmed account');
$page->login('unconfirmed@example.com', 'qwerty');
$I->see('You must confirm your account before logging in');

$I->amGoingTo('try to login with blocked account');
$page->login('blocked@example.com', 'qwerty');
$I->see('Your account has been blocked');

$I->amGoingTo('try to login with correct credentials');
$page->login('user@example.com', 'qwerty');
$I->dontSee('Login');
$I->see('Logout');
