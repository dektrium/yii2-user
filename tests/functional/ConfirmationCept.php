<?php

/**
 * @var Codeception\Scenario $scenario
 */

use tests\_fixtures\TokenFixture;
use yii\helpers\Url;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that confirmation works');
$I->haveFixtures(['token' => TokenFixture::className()]);

$I->amGoingTo('check that error is showed when token expired');
$token = $I->grabFixture('token', 'expired_confirmation');
$I->amOnPage(Url::toRoute(['/user/registration/confirm', 'id' => $token->user_id, 'code' => $token->code]));
$I->see('The confirmation link is invalid or expired. Please try requesting a new one.');

$I->amGoingTo('check that user get confirmed');
$token = $I->grabFixture('token', 'confirmation');
$I->amOnPage(Url::toRoute(['/user/registration/confirm', 'id' => $token->user_id, 'code' => $token->code]));
$I->see('Thank you, registration is now complete.');
$I->see('Logout');
