<?php

use tests\codeception\_pages\ResendPage;

$I = new FunctionalTester($scenario);
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
$page->resend($user->email);
$I->see('We need to confirm your email address');
