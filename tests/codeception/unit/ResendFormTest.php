<?php

namespace dektrium\user\tests;

use dektrium\user\models\ResendForm;
use tests\codeception\fixtures\UserFixture;
use yii\codeception\TestCase;

class ResendFormTest extends TestCase
{
    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/codeception/fixtures/data/init_user.php'
            ],
        ];
    }

    public function testValidateEmail()
    {
        $form = new ResendForm();
        $user = $this->getFixture('user')->getModel('user');
        $form->setAttributes([
            'email' => $user->email,
        ]);
        $this->assertFalse($form->validate());

        $form = new ResendForm();
        $user = $this->getFixture('user')->getModel('unconfirmed');
        $form->setAttributes([
            'email' => $user->email,
        ]);
        $this->assertTrue($form->validate());
    }
}
