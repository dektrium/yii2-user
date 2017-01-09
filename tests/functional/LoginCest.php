<?php
use dektrium\user\service\ConfirmationService;
use tests\_fixtures\UserFixture;
use tests\_pages\LoginPage;

/**
 * Created by PhpStorm.
 * User: dmeroff
 * Date: 06.01.17
 * Time: 19:04
 */

class LoginCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures(['user' => UserFixture::className()]);
    }

    public function testLoginWithDisabledConfirmation(FunctionalTester $I)
    {
        Yii::$container->set(ConfirmationService::className(), [
            'isEnabled' => false,
        ]);

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with unconfirmed account');
        $user = $I->grabFixture('user', 'unconfirmed');
        $page->login($user->email, 'qwerty');
        $I->dontSee('Login');
        $I->see('Logout');
    }

    public function testLoginWithEnabledEmailConfirmation(FunctionalTester $I)
    {
        Yii::$container->set(ConfirmationService::className(), [
            'isEnabled' => true,
            'isEmailConfirmationEnabled' => true,
        ]);

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with unconfirmed account');
        $user = $I->grabFixture('user', 'unconfirmed');
        $page->login($user->email, 'qwerty');
        $I->see('You need to confirm your email address');
    }

    public function testLoginWithEnabledAdminApproval(FunctionalTester $I)
    {
        Yii::$container->set(ConfirmationService::className(), [
            'isEnabled' => true,
            'isEmailConfirmationEnabled' => false,
            'isAdminApprovalEnabled' => true,
        ]);

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with unconfirmed account');
        $user = $I->grabFixture('user', 'unconfirmed_by_admin');
        $page->login($user->email, 'qwerty');
        $I->see('Your account needs to be approved by administrator');

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with confirmed account');
        $user = $I->grabFixture('user', 'confirmed_by_admin');
        $page->login($user->email, 'qwerty');
        $I->see('Logout');
    }

    public function testLoginWithEnabledBothEmailAndAdminConfirmation(FunctionalTester $I)
    {
        Yii::$container->set(ConfirmationService::className(), [
            'isEnabled' => true,
            'isEmailConfirmationEnabled' => true,
            'isAdminApprovalEnabled' => true,
        ]);

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with unconfirmed account');
        $user = $I->grabFixture('user', 'unconfirmed_by_admin');
        $page->login($user->email, 'qwerty');
        $I->see('Your account needs to be approved by administrator');

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with unconfirmed account');
        $user = $I->grabFixture('user', 'confirmed_by_admin');
        $page->login($user->email, 'qwerty');
        $I->see('You need to confirm your email address');

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with confirmed account');
        $user = $I->grabFixture('user', 'confirmed_by_email_and_admin');
        $page->login($user->email, 'qwerty');
        $I->see('Logout');
    }

    public function testCommonLoginFunctions(FunctionalTester $I)
    {
        Yii::$container->set(ConfirmationService::className(), [
            'isEnabled' => true,
            'isEmailConfirmationEnabled' => true,
            'isAdminApprovalEnabled' => false,
        ]);

        $page = LoginPage::openBy($I);
        $I->amGoingTo('try to login with empty credentials');
        $page->login('', '');
        $I->expectTo('see validations errors');
        $I->see('Login cannot be blank.');
        $I->see('Password cannot be blank.');

        $I->amGoingTo('try to login with blocked account');
        $user = $I->grabFixture('user', 'blocked');
        $page->login($user->email, 'qwerty');
        $I->see('Your account has been blocked');

        $I->amGoingTo('try to login with wrong credentials');
        $user = $I->grabFixture('user', 'user');
        $page->login($user->email, 'wrong');
        $I->expectTo('see validations errors');
        $I->see('Invalid login or password');

        $I->amGoingTo('try to login with correct credentials');
        $page->login($user->email, 'qwerty');
        $I->dontSee('Login');
        $I->see('Logout');
    }
}