<?php

/**
 * @var Codeception\Scenario $scenario
 */

use dektrium\user\models\Token;
use dektrium\user\models\User;
use tests\_fixtures\ProfileFixture;
use tests\_fixtures\UserFixture;
use tests\_pages\LoginPage;
use tests\_pages\SettingsPage;
use yii\helpers\Html;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that account settings page work');
$I->haveFixtures(['user' => UserFixture::className(), 'profile' => ProfileFixture::className()]);

$page = LoginPage::openBy($I);
$user = $I->grabFixture('user', 'user');
$page->login($user->username, 'qwerty');

$page = SettingsPage::openBy($I);

$I->amGoingTo('check that current password is required and must be valid');
$page->update($user->email, $user->username, 'wrong');
$I->see('Current password is not valid');

$I->amGoingTo('check that email is changing properly');
$page->update('new_user@example.com', $user->username, 'qwerty');
$I->seeRecord(User::className(), ['email' => $user->email, 'unconfirmed_email' => 'new_user@example.com']);
$I->see('A confirmation message has been sent to your new email address');
$user  = $I->grabRecord(User::className(), ['id' => $user->id]);
$token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRM_NEW_EMAIL]);
/** @var yii\swiftmailer\Message $message */
$message = $I->grabLastSentEmail();
$I->assertArrayHasKey($user->unconfirmed_email, $message->getTo());
$I->assertContains(Html::encode($token->getUrl()), utf8_encode(quoted_printable_decode($message->getSwiftMessage()->toString())));

Yii::$app->user->logout();

$I->amGoingTo('log in using new email address before clicking the confirmation link');
$page = LoginPage::openBy($I);
$page->login('new_user@example.com', 'qwerty');
$I->see('Invalid login or password');

$I->amGoingTo('log in using new email address after clicking the confirmation link');
$user->attemptEmailChange($token->code);
$page->login('new_user@example.com', 'qwerty');
$I->see('Logout');
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_user@example.com',
    'unconfirmed_email' => null,
]);

$I->amGoingTo('reset email changing process');
$page = SettingsPage::openBy($I);
$page->update('user@example.com', $user->username, 'qwerty');
$I->see('A confirmation message has been sent to your new email address');
$I->seeRecord(User::className(), [
    'id'    => 1,
    'email' => 'new_user@example.com',
    'unconfirmed_email' => 'user@example.com',
]);
$page->update('new_user@example.com', $user->username, 'qwerty');
$I->see('Your account details have been updated');
$I->seeRecord(User::className(), [
    'id'    => 1,
    'email' => 'new_user@example.com',
    'unconfirmed_email' => null,
]);
$I->amGoingTo('change username and password');
$page->update('new_user@example.com', 'nickname', 'qwerty', '123654');
$I->see('Your account details have been updated');
$I->seeRecord(User::className(), [
    'username' => 'nickname',
    'email'    => 'new_user@example.com',
]);

Yii::$app->user->logout();

$I->amGoingTo('login with new credentials');
$page = LoginPage::openBy($I);
$page->login('nickname', '123654');
$I->see('Logout');
