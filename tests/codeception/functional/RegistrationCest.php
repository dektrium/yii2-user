<?php

namespace dektrium\user\tests;

use dektrium\user\models\Token;
use dektrium\user\models\User;
use dektrium\user\Module;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\RegistrationPage;
use yii\helpers\Html;

class RegistrationCest
{
    public function _after()
    {
        \Yii::$container->set(Module::className(), [
            'enableConfirmation'       => true,
            'enableGeneratingPassword' => false,
        ]);
    }

    /**
     * Tests registration with email, username and password without any confirmation.
     * @param \dektrium\user\tests\FunctionalTester $I
     */
    public function testRegistration(FunctionalTester $I)
    {
        \Yii::$container->set(Module::className(), [
            'enableConfirmation'       => false,
            'enableGeneratingPassword' => false,
        ]);

        $page = RegistrationPage::openBy($I);

        $I->amGoingTo('try to register with empty credentials');
        $page->register('', '', '');
        $I->see('Username cannot be blank');
        $I->see('Email cannot be blank');
        $I->see('Password cannot be blank');

        $I->amGoingTo('try to register with already used email and username');
        $user = $I->getFixture('user')->getModel('user');
        $page->register($user->email, $user->username, 'qwerty');
        $I->see(Html::encode('This username has already been taken'));
        $I->see(Html::encode('This email address has already been taken'));

        $page->register('tester@example.com', 'tester', 'tester');
        $I->see('Your account has been created and a message with further instructions has been sent to your email');
        $user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
        $I->assertTrue($user->isConfirmed);

        $page = LoginPage::openBy($I);
        $page->login('tester', 'tester');
        $I->see('Logout');
    }

    /**
     * Tests registration when confirmation message is sent.
     * @param FunctionalTester $I
     */
    public function testRegistrationWithConfirmation(FunctionalTester $I)
    {
        \Yii::$container->set(Module::className(), [
            'enableConfirmation' => true,
        ]);
        $page = RegistrationPage::openBy($I);
        $page->register('tester@example.com', 'tester', 'tester');
        $I->see('Your account has been created and a message with further instructions has been sent to your email');
        $user  = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
        $token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRMATION]);
        $I->seeInEmail(Html::encode($token->url));
        $I->assertFalse($user->isConfirmed);
    }

    /**
     * Tests registration when password is generated automatically and sent to user.
     * @param FunctionalTester $I
     */
    public function testRegistrationWithoutPassword(FunctionalTester $I)
    {
        \Yii::$container->set(Module::className(), [
            'enableConfirmation'       => false,
            'enableGeneratingPassword' => true,
        ]);
        $page = RegistrationPage::openBy($I);
        $page->register('tester@example.com', 'tester');
        $I->see('Your account has been created and a message with further instructions has been sent to your email');
        $user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
        $I->assertEquals('tester', $user->username);
        $I->seeInEmail('We have generated a password for you');
    }
}