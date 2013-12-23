<?php namespace models;

use dektrium\user\models\User;
use yii\codeception\TestCase;

class RecoverableTest extends TestCase
{
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