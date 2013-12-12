<?php
$I = new TestGuy($scenario);
$I->wantTo('test confirmation accounts');
$I->amOnPage('?r=user/registration/confirm&id=2&token=NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6');
$I->see('Confirmation token is invalid');
$token = 'ediCJUtMifAikHaYkL2Kz6LakTN50fa4';
$I->haveInDatabase('user', [
	'email' => 'foobar@example.com',
	'username' => 'foobar',
	'confirmation_token' => $token,
	'confirmation_sent_time' => time()
]);
$I->amOnPage('?r=user/registration/confirm&id=3' . '&token=' . $token);
$I->see('Your account has been confirmed');
