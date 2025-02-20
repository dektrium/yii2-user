<?php

/**
 * @var Codeception\Scenario $scenario
 */

use tests\_fixtures\UserFixture;
use tests\_pages\UpdatePage;
use tests\_pages\LoginPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that user update works');
$I->haveFixtures(['user' => UserFixture::className()]);

$loginPage = LoginPage::openBy($I);
$user = $I->grabFixture('user', 'user');
$loginPage->login($user->email, 'qwerty');

$page = UpdatePage::openBy($I, ['id' => $user->id]);

$page->update('user', 'updated_user@example.com', 'new_pass');
$I->see('Account details have been updated');

Yii::$app->user->logout();
LoginPage::openBy($I)->login('updated_user@example.com', 'new_pass');
$I->see('Logout');
