<?php

use dektrium\user\tests\_pages\UpdatePage;
use dektrium\user\tests\_pages\LoginPage;

$I = new TestGuy($scenario);
$I->wantTo('ensure that user update works');

$loginPage = LoginPage::openBy($I);
$user = $I->getFixture('user')->getModel('user');
$loginPage->login($user->email, 'qwerty');

$page = UpdatePage::openBy($I, ['id' => 2]);

$page->update('new_toster', 'new_toster@example.com');
$I->see('User has been updated');
$I->see('new_toster');
$I->see('new_toster@example.com');
