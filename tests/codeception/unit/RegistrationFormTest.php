<?php

namespace dektrium\user\tests;

use Codeception\Specify;
use dektrium\user\helpers\Password;
use dektrium\user\models\RegistrationForm;
use dektrium\user\models\Token;
use dektrium\user\models\User;
use tests\codeception\_fixtures\UserFixture;
use yii\codeception\TestCase;

class RegistrationFormTest extends TestCase
{
    use Specify;

    /** @var RegistrationForm */
    protected $model;

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/codeception/_fixtures/data/init_user.php'
            ],
        ];
    }

    public function testValidationRules()
    {
        $this->model = new RegistrationForm();

        verify('username is required', $this->model->validate(['username']))->false();
        $this->model->username = \Yii::$app->security->generateRandomKey();
        verify('username is too long', $this->model->validate(['username']))->false();
        $this->model->username = '!@# абв';
        verify('username contains invalid characters', $this->model->validate(['username']))->false();
        $this->model->username = 'user';
        verify('username is already using', $this->model->validate(['username']))->false();
        $this->model->username = 'perfect_name';
        verify('username is ok', $this->model->validate(['username']))->true();

        verify('email is required', $this->model->validate(['email']))->false();
        $this->model->email = 'not valid email';
        verify('email is not email', $this->model->validate(['email']))->false();
        $this->model->email = 'user@example.com';
        verify('email is already using', $this->model->validate(['email']))->false();
        $this->model->email = 'perfect@example.com';
        verify('email is ok', $this->model->validate(['email']))->true();

        verify('password is required', $this->model->validate(['password']))->false();
        $this->model->password = '12345';
        verify('password is too short', $this->model->validate(['password']))->false();
        $this->model->password = 'superSecretPa$$word';
        verify('password is ok', $this->model->validate(['password']))->true();
    }

    public function testRegister()
    {
        $this->model = new RegistrationForm();
        $this->model->setAttributes([
            'email'    => 'foobar@example.com',
            'username' => 'foobar',
            'password' => 'foobar'
        ]);

        /** @var User $user */
        verify($this->model->register())->true();

        $user = User::findOne(['email' => 'foobar@example.com']);

        verify('$user is instance of User', $user instanceof User)->true();
        verify('email is valid', $user->email)->equals($this->model->email);
        verify('username is valid', $user->username)->equals($this->model->username);
        verify('password is valid', Password::validate($this->model->password, $user->password_hash))->true();

        $token = Token::findOne(['user_id' => $user->id, 'type' => Token::TYPE_CONFIRMATION]);
        verify($token)->notNull();

        $mock = $this->getMock(RegistrationForm::className(), ['validate']);
        $mock->expects($this->once())->method('validate')->will($this->returnValue(false));
        verify($mock->register())->false();
    }
}