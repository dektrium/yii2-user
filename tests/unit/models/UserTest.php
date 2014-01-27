<?php namespace models;

use dektrium\user\models\User;
use yii\codeception\TestCase;

class UserTest extends TestCase
{
	public function testRegister()
	{
		$user = new User(['scenario' => 'register']);
		$user->setAttributes([
			'username' => 'tester',
			'email' => 'tester@example.com',
			'password' => 'tester'
		]);
		$this->assertTrue($user->register());
	}

	public function testRegisterWithGeneratePassword()
	{
		$user = new User(['scenario' => 'register']);
		$user->setAttributes([
			'username' => 'tester2',
			'email' => 'tester2@example.com',
		]);
		$this->assertFalse($user->register());
		\Yii::$app->getModule('user')->generatePassword = true;
		$this->assertTrue($user->register(true));
	}

	public function testGetIsConfirmed()
	{
		\Yii::$app->getModule('user')->confirmable = true;
		$user = User::find(1);
		$this->assertTrue($user->getIsConfirmed());
		$user = User::find(2);
		$this->assertFalse($user->getIsConfirmed());
	}

	public function testGetConfirmationUrl()
	{
		\Yii::$app->getModule('user')->confirmable = true;
		$user = User::find(2);
		$url = $user->getConfirmationUrl();
		$this->assertEquals('http://localhost/index.php?r=user/registration/confirm&id=2&token=' . $user->confirmation_token, $url);
		$user = User::find(1);
		$this->assertNull($user->getConfirmationUrl());
	}

	public function testSendConfirmationMessage()
	{
		\Yii::$app->getModule('user')->confirmable = true;
		$user = User::find(1);
		$this->assertTrue($user->getIsConfirmed());
		$user->resend();
		$this->assertFalse($user->getIsConfirmed());
	}

	public function testGetIsConfirmationPeriodExpired()
	{
		\Yii::$app->getModule('user')->confirmable = true;
		\Yii::$app->getModule('user')->confirmWithin = 86400;
		$user = new User([
			'confirmation_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
			'confirmation_sent_time' => time() - 192800
		]);
		$this->assertTrue($user->getIsConfirmationPeriodExpired());
		$user = new User([
			'confirmation_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
			'confirmation_sent_time' => time()
		]);
		$this->assertFalse($user->getIsConfirmationPeriodExpired());
	}

	public function testConfirm()
	{
		\Yii::$app->getModule('user')->confirmable = true;
		$user = User::find(2);
		$user->resend();
		$this->assertFalse($user->getIsConfirmed());
		$user->confirm();
		$this->assertTrue($user->getIsConfirmed());
	}

	public function testGetIsRecoveryPeriodExpired()
	{
		\Yii::$app->getModule('user')->recoverable = true;
		$user = new User([
			'recovery_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
			'recovery_sent_time' => time() - 86400
		]);
		$this->assertTrue($user->getIsRecoveryPeriodExpired());
		$user = new User([
			'recovery_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
			'recovery_sent_time' => time()
		]);
		$this->assertFalse($user->getIsRecoveryPeriodExpired());
	}

	public function testGetRecoveryUrl()
	{
		\Yii::$app->getModule('user')->recoverable = true;
		$user = new User([
			'id' => 999,
			'recovery_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
			'recovery_sent_time' => time() - 86400
		]);
		$this->assertEquals(
			 'http://localhost/index.php?r=user/recovery/reset&id=999&token=NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
				 $user->getRecoveryUrl()
		);
	}

	public function testSendRecoveryMessage()
	{
		\Yii::$app->getModule('user')->recoverable = true;
		$user = User::find(1);
		$user->sendRecoveryMessage();
		$this->assertNotNull($user->recovery_token);
		$this->assertNotNull($user->recovery_sent_time);
	}
}