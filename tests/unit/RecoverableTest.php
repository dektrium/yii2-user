<?php
use Codeception\Util\Stub;

class RecoverableTest extends \Codeception\TestCase\Test
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

	public function testGetIsRecoveryPeriodExpired()
	{
		Yii::$app->getModule('user')->recoverable = true;
		$user = new \dektrium\user\models\User([
			'recovery_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
			'recovery_sent_time' => time() - 86400
		]);
		$this->assertTrue($user->getIsRecoveryPeriodExpired());
		$user = new \dektrium\user\models\User([
			'recovery_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
			'recovery_sent_time' => time()
		]);
		$this->assertFalse($user->getIsRecoveryPeriodExpired());
	}

	public function testGetRecoveryUrl()
	{
		Yii::$app->getModule('user')->recoverable = true;
		$user = new \dektrium\user\models\User([
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
		Yii::$app->getModule('user')->recoverable = true;
		$user = \dektrium\user\models\User::find(1);
		$user->sendRecoveryMessage();
		$this->assertNotNull($user->recovery_token);
		$this->assertNotNull($user->recovery_sent_time);
	}
}