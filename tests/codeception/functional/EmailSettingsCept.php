<?php

use tests\codeception\_pages\EmailSettingsPage;
use tests\codeception\_pages\LoginPage;
use dektrium\user\models\User;
use dektrium\user\models\Token;
use yii\helpers\Html;

$I = new FunctionalTester($scenario);
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
$token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRMATION]);
$I->seeInEmail(Html::encode($token->getUrl()));
$I->seeInEmailRecipients($user->unconfirmed_email);

Yii::$app->getUser()->logout();

$I->amGoingTo('login with new email');
$loginPage = LoginPage::openBy($I);
$loginPage->login('new_email@example.com', 'qwerty');
$I->see('Invalid login or password');
$user->attemptConfirmation($token->code);
$loginPage = LoginPage::openBy($I);
$loginPage->login('user@example.com', 'qwerty');
$I->see('Invalid login or password');
$loginPage->login('new_email@example.com', 'qwerty');
$I->see('Logout');
$user = $I->grabRecord(User::className(), ['id' => $user->id]);
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_email@example.com',
    'unconfirmed_email' => null
]);

// try to reset email change
$page = EmailSettingsPage::openBy($I);
$page->updateEmail('qwerty', 'user@example.com');
$I->see('Before your email will be changed we need you to confirm your new email address');
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_email@example.com',
    'unconfirmed_email' => 'user@example.com'
]);
$page->updateEmail('qwerty', 'new_email@example.com');
$I->see('Email change has been cancelled');
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_email@example.com',
    'unconfirmed_email' => null
]);