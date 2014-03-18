<?php

namespace dektrium\user\tests\forms;

use dektrium\user\forms\Login;
use dektrium\user\tests\_fixtures\UserFixture;
use yii\codeception\TestCase;

class LoginTest extends TestCase
{
    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/_fixtures/init_user.php'
            ],
        ];
    }

    public function testLogin()
    {
        $form = new Login();
        $form->setAttributes([
            'login' => 'user',
            'password' => 'qwerty'
        ]);
        $this->assertFalse($form->validate());
        $form->setAttributes([
            'login' => 'user@example.com',
            'password' => 'wrong'
        ]);
        $form->setAttributes([
            'login' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($form->validate());
    }

    public function testLoginByUsername()
    {
        $form = new Login();
        \Yii::$app->getModule('user')->loginType = 'username';
        $form->setAttributes([
            'login' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertFalse($form->validate());
        $form->setAttributes([
            'login' => 'user',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($form->validate());
    }

    public function testLoginByEmailOrUsername()
    {
        $form = new Login();
        \Yii::$app->getModule('user')->loginType = 'both';
        $form->setAttributes([
            'login' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($form->validate());
        $form->setAttributes([
            'login' => 'user',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($form->validate());
    }

    public function testUnconfirmedLogin()
    {
        \Yii::$app->getModule('user')->confirmable = true;
        \Yii::$app->getModule('user')->allowUnconfirmedLogin = false;
        $form = new Login();
        $form->setAttributes([
            'login' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($form->validate());
        $form->setAttributes([
            'login' => 'unconfirmed@example.com',
            'password' => 'unconfirmed'
        ]);
        $this->assertFalse($form->validate());
        \Yii::$app->getModule('user')->allowUnconfirmedLogin = true;
        $this->assertTrue($form->validate());
    }

    public function testCaptcha()
    {
        $form = new Login();
        $form->setAttributes([
            'login' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($form->validate());
        \Yii::$app->getModule('user')->captcha[] = 'login';
        $form = new Login();
        $form->setAttributes([
            'login' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertFalse($form->validate());
    }
}
