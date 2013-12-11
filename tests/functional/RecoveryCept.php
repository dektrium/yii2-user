<?php
$I = new TestGuy($scenario);
$I->wantTo('recover my password');
$I->amOnPage('/?r=user/recovery/request');
$I->fillField('#recovery-form-email', 'user@example.com');
$I->click('Request password recovery');
$I->see('You have been sent an email with instructions on how to reset your password.');

$I->haveInDatabase('user', [
    'id' => 3,
    'recovery_token' => 'dghFKJA6JvjTKLAwyE5w2XD9b2lmBXLE',
    'recovery_sent_time' => time() - 86400
]);
$I->amOnPage('/?r=user/recovery/reset&id=3&token=dghFKJA6JvjTKLAwyE5w2XD9b2lmBXLE');
$I->see('Recovery token is invalid');

$I->haveInDatabase('user', [
    'id' => 4,
    'username' => 'tester',
    'email' => 'tester@example.com',
    'auth_key' => 'mvhZA1A6JvjTKLAwyE5w2XD9b2lmBXLE',
    'recovery_token' => 'ediCJUtMifAikHaYkL2Kz6LakTN50fa4',
    'recovery_sent_time' => time()
]);
$I->amOnPage('/?r=user/recovery/reset&id=4&token=ediCJUtMifAikHaYkL2Kz6LakTN50fa4');
$I->fillField('#recovery-form-password', 'qwerty');
$I->click('Reset password');
$I->see('Your password has been reset');

$I->amOnPage('?r=user/auth/login');
$I->fillField('#login-form-login', 'tester@example.com');
$I->fillField('#login-form-password', 'qwerty');
$I->click('Log in');
$I->dontSee('Invalid login or password');