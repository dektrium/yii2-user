<?php

use dektrium\user\tests\_pages\PasswordSettingsPage;
use dektrium\user\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that password settings works');

$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');

$I->amGoingTo('try to change current password');
$page = PasswordSettingsPage::openBy($I);
$page->updatePassword('wrong', 'new_password');
$I->see('Current password is not valid');
$page->updatePassword('qwerty', 'new_password');
$I->see('Password updated successfully');

$I->amGoingTo('try to change password back');
$page->updatePassword('qwerty', 'qwerty');
$I->see('Current password is not valid');
$page->updatePassword('new_password', 'qwerty');
$I->see('Password updated successfully');
