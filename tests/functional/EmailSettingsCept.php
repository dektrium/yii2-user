<?php

use dektrium\user\tests\_pages\EmailSettingsPage;
use dektrium\user\tests\_pages\LoginPage;
use dektrium\user\models\User;
use yii\helpers\Html;

$I = new TestGuy($scenario);
$I->wantTo('ensure that email settings works');

$loginPage = LoginPage::openBy($I);
$user = $I->getFixture('user')->getModel('user');
$loginPage->login($user->email, 'qwerty');

$I->amGoingTo('update email');
$page = EmailSettingsPage::openBy($I);
$page->updateEmail('wrong', 'new_email@example.com');
$I->see('Current password is not valid');

$page->updateEmail('qwerty', 'new_email@example.com');
$I->see('Before your email will be changed we need you to confirm your new email address');
$I->seeRecord(User::className(), [
    'id' => $user->id,
    'email' => 'user@example.com',
    'unconfirmed_email' => 'new_email@example.com'
]);
$user = $I->grabRecord(User::className(), ['id' => $user->id]);
$I->seeInEmail(Html::encode($user->getConfirmationUrl()));
$I->seeInEmailRecipients($user->unconfirmed_email);

Yii::$app->getUser()->logout();

$I->amGoingTo('login with new email');
$loginPage = LoginPage::openBy($I);
$loginPage->login('new_email@example.com', 'qwerty');
$I->see('Invalid login or password');
$user->confirm(false);
$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');
$I->see('Invalid login or password');
$loginPage->login('new_email@example.com', 'qwerty');
$I->see('Logout');
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_email@example.com',
    'unconfirmed_email' => null
]);
