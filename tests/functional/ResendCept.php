<?php

use tests\_pages\ResendPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that resending confirmation tokens works');

$page = ResendPage::openBy($I);

$I->amGoingTo('try to resend token to confirmed user');
$page->resend('user@example.com');
$I->see('This account has already been confirmed');

$I->seeInDatabase('user', ['confirmation_token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
$page->resend('unconfirmed@example.com');
$I->see('Awesome, almost there! We need to confirm your email address');
$I->dontSeeInDatabase('user', ['confirmation_token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
