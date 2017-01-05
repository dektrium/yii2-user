<?php

/**
 * @var Codeception\Scenario $scenario
 */

use tests\_fixtures\UserFixture;
use tests\_pages\ResendPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that resending of confirmation tokens works');
$I->haveFixtures(['user' => UserFixture::className()]);

$I->amGoingTo('try to resend token to non-existent user');
$page = ResendPage::openBy($I);
$page->resend('foo@example.com');
$I->see('Confirmation message has been resent to your email address');

$I->amGoingTo('try to resend token to already confirmed user');
$page = ResendPage::openBy($I);
$user = $I->grabFixture('user', 'user');
$page->resend($user->email);
$I->see('Confirmation message has been resent to your email address');

$I->amGoingTo('try to resend token to unconfirmed user');
$page = ResendPage::openBy($I);
$user = $I->grabFixture('user', 'unconfirmed');
$page->resend($user->email);
$I->see('Confirmation message has been resent to your email address');
