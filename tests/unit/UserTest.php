<?php
use Codeception\Util\Stub;

class UserTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRegistration()
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

    public function testLogin()
    {
        $this->assertTrue(Yii::$app->getUser()->getIsGuest());
        $user = new \dektrium\user\models\User(['scenario' => 'login']);
        $user->setAttributes([
            'email' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($user->login());
        $this->assertFalse(Yii::$app->getUser()->getIsGuest());
        $this->assertEquals(1, Yii::$app->getSession()->get('user.id'));
        $this->assertEquals('user', Yii::$app->getSession()->get('user.username'));
        $this->assertEquals('user@example.com', Yii::$app->getSession()->get('user.email'));
    }
}