<?php
$I = new TestGuy($scenario);
$I->wantTo('test registration');
$I->amOnPage('?r=user/registration/register');
$I->see('Sign up');
$I->fillField('#user-register-form-username', 'tester');
$I->fillField('#user-register-form-email', 'tester@example.com');
$I->fillField('#user-register-form-password', 'tester');
$I->click('Register');
$I->see('Awesome, almost there! We need to confirm your email address');
$I->seeInDatabase('user', ['email' => 'tester@example.com', 'username' => 'tester']);
