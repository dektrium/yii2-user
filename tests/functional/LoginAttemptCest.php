<?php

use tests\_fixtures\UserFixture;
use tests\_pages\LoginPage;
use dektrium\user\Module;

class LoginAttemptCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures(['user' => UserFixture::className()]);
    }

    public function _after(FunctionalTester $I)
    {
        \Yii::$container->set(Module::className(), [
            'enableLockLoginAfterFailedLogin' => false,
            'numberOfAllowedInvalidLoginAttempts' => 3,
            'secondsAfterLastInvalidLoginToResetCounter' => 3600,
        ]);
    }

    /**
     * Test log in with invalid credentials.
     * @param FunctionalTester $I
     */
    public function testLoginSecurity(FunctionalTester $I)
    {
        \Yii::$container->set(Module::className(), [
            'enableLockLoginAfterFailedLogin' => true,
            'numberOfAllowedInvalidLoginAttempts' => 3,
            'secondsAfterLastInvalidLoginToResetCounter' => 3600,
        ]);

        $page = LoginPage::openBy($I);

        $I->wantTo('ensure that login security works');

        $user = $I->grabFixture('user', 'user');

        $I->amGoingTo('1. attempt: try to login with wrong credentials');
        $page->login($user->email, 'wrong');
        $I->expectTo('see validations errors');
        $I->see('Invalid login or password');

        $I->amGoingTo('2. attempt: try to login with wrong credentials');
        $page->login($user->email, 'wrong');
        $I->expectTo('see validations errors');
        $I->see('Invalid login or password');

        $I->amGoingTo('3. attempt: try to login with wrong credentials');
        $page->login($user->email, 'wrong');
        $I->expectTo('see validations errors');
        $I->see('Invalid login or password');

        $I->amGoingTo('4. attempt: try to login with wrong credentials');
        $page->login($user->email, 'wrong');
        $I->expectTo('see login attempt error');
        $I->see('Login is locked for 1 seconds');
       
        sleep(2);
        
        $I->amGoingTo('5. attempt: try to login with wrong credentials');
        $page->login($user->email, 'wrong');
        $I->expectTo('see login attempt error');
        $I->see('Invalid login or password. Login is locked for 4 seconds.');

        $I->amGoingTo('6. attempt: try to login with wrong credentials');
        $page->login($user->email, 'wrong');
        $I->expectTo('see login attempt error');
        $I->see('Invalid login or password. Login is locked for');
        
        sleep(5);
        
        $I->amGoingTo('try to login with correct credentials');
        $page->login($user->email, 'qwerty');
        $I->dontSee('Login');
        $I->see('Logout');
    }
}
