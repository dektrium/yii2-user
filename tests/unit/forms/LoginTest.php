<?php

namespace dektrium\user\tests\forms;

use Codeception\Specify;
use dektrium\user\forms\Login;
use dektrium\user\tests\_fixtures\UserFixture;
use yii\codeception\TestCase;

class LoginTest extends TestCase
{
    use Specify;

    /**
     * @var Login
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

    public function testLogin()
    {
        $this->form = new Login();

        $this->specify('should not allow logging in blocked users', function () {
            $this->form->setAttributes([
                'email'    => 'blocked@example.com',
                'password' => 'qwerty'
            ]);
            verify($this->form->validate())->false();
            verify($this->form->getErrors('email'))->contains('Your account has been blocked');
        });

        $this->specify('should not allow logging in unconfirmed users', function () {
            \Yii::$app->getModule('user')->confirmable = true;
            \Yii::$app->getModule('user')->allowUnconfirmedLogin = false;
            $this->form->setAttributes([
                'email' => 'user@example.com',
                'password' => 'qwerty'
            ]);
            verify($this->form->validate())->true();
            $this->form->setAttributes([
                'email' => 'unconfirmed@example.com',
                'password' => 'unconfirmed'
            ]);
            verify($this->form->validate())->false();
            \Yii::$app->getModule('user')->allowUnconfirmedLogin = true;
            verify($this->form->validate())->true();
        });

        $this->specify('should log the user in with correct credentials', function () {
            verify($this->form->validate())->false();
            $this->form->setAttributes([
                'email' => 'user@example.com',
                'password' => 'wrong'
            ]);
            verify($this->form->validate())->false();
            $this->form->setAttributes([
                'email' => 'user@example.com',
                'password' => 'qwerty'
            ]);
            verify($this->form->validate())->true();
        });
    }
}
