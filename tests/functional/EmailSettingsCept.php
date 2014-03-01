<?php

use tests\_pages\EmailSettingsPage;
use tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that profile settings works');

$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');

$page = EmailSettingsPage::openBy($I);
$page->update('wrong', 'new_email@example.com');
$I->see('Current password is not valid');

$page->update('qwerty', 'new_email@example.com');
$I->see('Before your email will be changed we need you to confirm your new email address');
$I->seeInDatabase('user', [
	'id' => 1,
	'email' => 'user@example.com',
	'unconfirmed_email' => 'new_email@example.com'
]);

Yii::$app->getUser()->logout();

$loginPage = LoginPage::openBy($I);
$loginPage->login('new_email@example.com', 'qwerty');
$I->see('Invalid login or password');

\dektrium\user\models\User::find(1)->confirm(false);

$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');
$I->see('Invalid login or password');

$loginPage->login('new_email@example.com', 'qwerty');
$I->see('Logout');
$I->seeInDatabase('user', [
	'id' => 1,
	'email' => 'new_email@example.com',
	'unconfirmed_email' => null
]);

Yii::$app->getUser()->logout();