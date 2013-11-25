<?php
$I = new TestGuy($scenario);
$I->wantTo('test resending confirmation tokens');
$I->amOnPage('?r=user/registration/resend');
$I->fillField('#resend-form-email', 'user@example.com');
$I->click('Send');
$I->see('This account has already been confirmed');
$I->amOnPage('?r=user/registration/resend');
$I->fillField('#resend-form-email', 'unconfirmed@example.com');
$I->seeInDatabase('user', ['confirmation_token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
$I->click('Send');
$I->see('Awesome, almost there! We need to confirm your email address');
$I->dontSeeInDatabase('user', ['confirmation_token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
