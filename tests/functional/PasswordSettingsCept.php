<?php

use dektrium\user\tests\_pages\PasswordSettingsPage;
use dektrium\user\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that password settings works');

$loginPage = LoginPage::openBy($I);
$user = $I->getFixture('user')->getModel('user');
$loginPage->login($user->email, 'qwerty');

$I->amGoingTo('try to change current password');
$page = PasswordSettingsPage::openBy($I);
$page->updatePassword('wrong', 'new_password');
$I->see('Current password is not valid');
$page->updatePassword('qwerty', 'new_password');
$I->see('Password has been changed');

$I->amGoingTo('try to change password back');
$page->updatePassword('qwerty', 'qwerty');
$I->see('Current password is not valid');
$page->updatePassword('new_password', 'qwerty');
$I->see('Password has been changed');
