<?php

use yii\helpers\Url;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that confirmation works');

$I->amGoingTo('check that error is showed when token expired');
$token = $I->getFixture('token')->getModel('expired_confirmation');
$I->amOnPage(Url::toRoute(['/user/registration/confirm', 'id' => $token->user_id, 'code' => $token->code]));
$I->see('We are sorry but your confirmation token is out of date');

$I->amGoingTo('check that user get confirmed');
$token = $I->getFixture('token')->getModel('confirmation');
$I->amOnPage(Url::toRoute(['/user/registration/confirm', 'id' => $token->user_id, 'code' => $token->code]));
$I->see('You have successfully confirmed your email address. You may sign in using your credentials now');
$I->see('Logout');
