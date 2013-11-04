<?php

use Codeception\Util\Stub;

class TrackableTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

    public function testRegistrationTrack()
    {
        $user = new \dektrium\user\models\User(['scenario' => 'register']);
        $user->attachBehavior('trackable', \dektrium\user\behaviors\Trackable::className());
        $user->setAttributes([
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'tester'
        ]);
        $this->assertTrue($user->register());

        $this->codeGuy->seeInDatabase('user', [
            'username' => 'tester',
            'email' => 'tester@example.com',
            'registration_ip' => ip2long(Yii::$app->getRequest()->getUserIP()),
        ]);
    }

    public function testLoginTrack()
    {
        $user = new \dektrium\user\models\User(['scenario' => 'login']);
        $user->attachBehavior('trackable', \dektrium\user\behaviors\Trackable::className());
        $user->setAttributes([
            'email' => 'user@example.com',
            'password' => 'qwerty'
        ]);
        $this->assertTrue($user->login());
        $this->codeGuy->seeInDatabase('user', [
            'email' => 'user@example.com',
            'login_ip' => ip2long(Yii::$app->getRequest()->getUserIP()),
        ]);
    }
}