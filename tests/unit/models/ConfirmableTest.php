<?php namespace models;

use dektrium\user\models\User;
use yii\codeception\TestCase;

class ConfirmableTest extends TestCase
{
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
		$user->sendConfirmationMessage();
		$this->assertFalse($user->getIsConfirmed());
		$this->assertTrue(\Yii::$app->getSession()->hasFlash('confirmation_message_sent'));
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
		$user->sendConfirmationMessage();
		$this->assertFalse($user->getIsConfirmed());
		$user->confirm();
		$this->assertTrue($user->getIsConfirmed());
	}
}