<?php

namespace dektrium\user\tests\forms;

use dektrium\user\forms\Resend;
use dektrium\user\tests\_fixtures\UserFixture;
use yii\codeception\TestCase;

class ResendTest extends TestCase
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

    public function testValidateEmail()
    {
        $form = new Resend();
        $form->setAttributes([
            'email' => 'user@example.com',
        ]);
        $this->assertFalse($form->validate());

        $form = new Resend();
        $form->setAttributes([
            'email' => 'unconfirmed@example.com',
        ]);
        $this->assertTrue($form->validate());
    }

    public function testCaptcha()
    {
        $form = new Resend();
        $form->setAttributes([
            'email' => 'unconfirmed@example.com',
        ]);
        $this->assertTrue($form->validate());

        \Yii::$app->getModule('user')->captcha[] = 'resend';
        $form = new Resend();
        $form->setAttributes([
            'email' => 'unconfirmed@example.com',
        ]);
        $this->assertFalse($form->validate());
    }
}
