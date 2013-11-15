<?php
use Codeception\Util\Stub;

class RegistrationTest extends \Codeception\TestCase\Test
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

    public function testRegister()
    {
        $user = new \dektrium\user\models\User(['scenario' => 'register']);
        $user->setAttributes([
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'tester'
        ]);
        $this->assertTrue($user->register());
        $this->codeGuy->seeInDatabase('user', [
            'username' => 'tester',
            'email' => 'tester@example.com',
        ]);
    }

    public function testGenerateConfirmationData()
    {
        Yii::$app->getModule('user')->confirmable = true;
        $user = new \dektrium\user\models\User();
        $user->generateConfirmationData();
        $this->assertInternalType('int', $user->confirmation_sent_time);
        $this->assertInternalType('string', $user->confirmation_token);
        $this->assertNull($user->confirmation_time);
    }

    public function testSendConfirmationMessage()
    {
        Yii::$app->getModule('user')->confirmable = true;
        /** @var \dektrium\user\models\User $user */
        $user = \dektrium\user\models\User::find(1);
        $this->assertTrue($user->getIsConfirmed());
        $user->sendConfirmationMessage();
        $this->assertFalse($user->getIsConfirmed());
    }

    public function testGetConfirmationUrl()
    {
        Yii::$app->getModule('user')->confirmable = true;
        /** @var \dektrium\user\models\User $user */
        $user = \dektrium\user\models\User::find(2);
        $url = $user->getConfirmationUrl();
        $this->assertEquals('http://localhost/index.php?r=user/registration/confirm&id=2&confirmation_token='.$user->confirmation_token, $url);
    }

    public function testGetIsConfirmed()
    {
        Yii::$app->getModule('user')->confirmable = true;
        $user = \dektrium\user\models\User::find(2);
        $this->assertFalse($user->getIsConfirmed());
        $user = \dektrium\user\models\User::find(1);
        $this->assertTrue($user->getIsConfirmed());
    }

    public function testGetIsConfirmationPeriodExpired()
    {
        Yii::$app->getModule('user')->confirmable = true;
        Yii::$app->getModule('user')->confirmWithin = 86400;
        $user = new \dektrium\user\models\User([
            'confirmation_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
            'confirmation_sent_time' => time() - 192800
        ]);
        $this->assertTrue($user->getIsConfirmationPeriodExpired());
        $user = new \dektrium\user\models\User([
            'confirmation_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
            'confirmation_sent_time' => time()
        ]);
        $this->assertFalse($user->getIsConfirmationPeriodExpired());
    }

    public function testConfirm()
    {
        Yii::$app->getModule('user')->confirmable = true;
        $user =\dektrium\user\models\User::find(2);
        $user->generateConfirmationData();
        $this->assertFalse($user->getIsConfirmed());
        $user->confirm();
        $this->assertTrue($user->getIsConfirmed());
    }
}