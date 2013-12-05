<?php
use Codeception\Util\Stub;

class ResendTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    protected function _before()
    {
        $this->codeGuy->mockApplication();
        $controller = Stub::construct('\yii\web\Controller', ['test', Yii::$app->getModule('user')], ['__set' => null]);
        Yii::$app->controller = $controller;
    }

    protected function _after()
    {
        $this->codeGuy->destroyApplication();
    }

    public function testValidation()
    {
        $model = new \dektrium\user\forms\Resend();
        $model->email = 'foobar@example.com';
        $this->assertFalse($model->validate());
        $model->email = 'user@example.com';
        $this->assertFalse($model->validate());
        $model->email = 'unconfirmed@example.com';
        $this->assertTrue($model->validate());
    }

    public function testResend()
    {
        $model = new \dektrium\user\forms\Resend();
        $model->email = 'unconfirmed@example.com';
        $user = \dektrium\user\models\User::findByEmail('unconfirmed@example.com');
        $token = $user->confirmation_token;
        $time = $user->confirmation_sent_time;
        $this->assertTrue($model->resend());
        $user = \dektrium\user\models\User::findByEmail('unconfirmed@example.com');
        $this->assertNotEquals($time, $user->confirmation_sent_time);
        $this->assertNotEquals($token, $user->confirmation_token);
    }
}
