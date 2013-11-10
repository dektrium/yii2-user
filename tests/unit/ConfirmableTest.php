<?php
use Codeception\Util\Stub;

class ConfirmableTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;

	public function setUp()
	{
		parent::setUp();
		Yii::$app->getModule('user')->confirmable = true;
	}

    public function testConfirmationSend()
    {
        $user = new \dektrium\user\models\User(['scenario' => 'register']);
        $user->setAttributes([
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'tester'
        ]);
        $this->assertTrue($user->register());

        $user = \dektrium\user\models\User::findByEmail('tester@example.com');
        $this->assertNotNull($user->confirmation_sent_time);
        $this->assertNotNull($user->confirmation_token);
		$this->assertNull($user->confirmation_time);
    	$this->assertFalse($user->isConfirmed);

		$this->assertTrue($user->confirm());

		$this->assertNull($user->confirmation_sent_time);
		$this->assertNull($user->confirmation_token);
		$this->assertNotNull($user->confirmation_time);
		$this->assertTrue($user->isConfirmed);
	}

	public function testConfirmationTokenExpired()
	{
		$user = new \dektrium\user\models\User(['scenario' => 'register']);
		$user->setAttributes([
				'username' => 'tester',
				'email' => 'tester@example.com',
				'password' => 'tester'
			]);
		$this->assertTrue($user->register());

		$user = \dektrium\user\models\User::findByEmail('tester@example.com');
		$user->confirmation_sent_time -= 172800;
		$user->save(false);

		$this->assertFalse($user->confirm());
	}
}