<?php

use tests\_pages\PasswordSettingsPage;
use tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that profile settings works');

$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');

$page = PasswordSettingsPage::openBy($I);
$page->update('wrong', 'new_password');
$I->see('Current password is not valid');

$page->update('qwerty', 'new_password');
$I->see('Password updated successfully');

Yii::$app->getUser()->logout();

$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');
$I->see('Invalid login or password');

$loginPage->login('user@example.com', 'new_password');
$I->see('Logout');

Yii::$app->getUser()->logout();