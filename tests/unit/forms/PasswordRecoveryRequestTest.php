<?php

namespace dektrium\user\tests\forms;

use Codeception\Specify;
use dektrium\user\forms\PasswordRecoveryRequest;
use dektrium\user\tests\_fixtures\UserFixture;
use dektrium\user\tests\unit\TestCase;

class PasswordRecoveryRequestTest extends TestCase
{
    use Specify;

    /**
     * @var \dektrium\user\forms\PasswordRecoveryRequest
     */
    protected $form;

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

    public function testFormValidation()
    {
        $this->form = new PasswordRecoveryRequest();

        $this->specify('email is required', function () {
            $this->form->email = null;
            verify($this->form->validate(['email']))->false();
        });

        $this->specify('email exists', function () {
            $this->form->email = 'non-existing@email.com';
            verify($this->form->validate(['email']))->false();
        });

        \Yii::$app->getModule('user')->confirmable = true;

        $this->specify('email should be confirmed if confirmable is enabled', function () {
            $this->form->email = 'unconfirmed@example.com';
            verify($this->form->validate(['email']))->false();
            $this->form->email = 'user@example.com';
            verify($this->form->validate(['email']))->true();
        });
    }
}
