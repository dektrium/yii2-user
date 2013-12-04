<?php
use Codeception\Util\Stub;

class LoginTest extends \Codeception\TestCase\Test
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

    public function testLogin()
    {
        $model = new \dektrium\user\models\LoginForm();
        $this->assertFalse($model->login());
        $model->login = 'tester@example.com';
        $model->password = 'blablabla';
        $this->assertFalse($model->login());
        $model->login = 'user@example.com';
        $model->password = 'wrongpass';
        $this->assertFalse($model->login());
        $model->login = 'user@example.com';
        $model->password = 'qwerty';
        $this->assertTrue(Yii::$app->getUser()->getIsGuest());
        $this->assertTrue($model->login());
        $this->assertFalse(Yii::$app->getUser()->getIsGuest());
        Yii::$app->getUser()->logout();
    }

    public function testLoginByUsername()
    {
        Yii::$app->getModule('user')->loginType = 'username';
        $model = new \dektrium\user\models\LoginForm();
        $model->login = 'user';
        $model->password = 'qwerty';
        $this->assertTrue($model->login());
        $this->assertFalse(Yii::$app->getUser()->getIsGuest());
        Yii::$app->getUser()->logout();
    }

    public function testLoginByEmailOrUsername()
    {
        Yii::$app->getModule('user')->loginType = 'both';
        $model = new \dektrium\user\models\LoginForm();
        $model->login = 'user';
        $model->password = 'qwerty';
        $this->assertTrue($model->login());
        $this->assertFalse(Yii::$app->getUser()->getIsGuest());
        Yii::$app->getUser()->logout();
        $model->login = 'user@example.com';
        $model->password = 'qwerty';
        $this->assertTrue($model->login());
        $this->assertFalse(Yii::$app->getUser()->getIsGuest());
        Yii::$app->getUser()->logout();
    }

    public function testUnconfirmedLogin()
    {
        Yii::$app->getModule('user')->confirmable = true;
        Yii::$app->getModule('user')->allowUnconfirmedLogin = false;
        $model = new \dektrium\user\models\LoginForm();
        $model->login = 'unconfirmed@example.com';
        $model->password = 'unconfirmed';
        $this->assertFalse($model->login());
        $model->login = 'user@example.com';
        $model->password = 'qwerty';
        $this->assertTrue($model->login());
        Yii::$app->getModule('user')->allowUnconfirmedLogin = true;
        $model->login = 'unconfirmed@example.com';
        $model->password = 'unconfirmed';
        $this->assertTrue($model->login());
        Yii::$app->getUser()->logout();
    }
}
