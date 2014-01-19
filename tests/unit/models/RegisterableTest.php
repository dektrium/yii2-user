<?php namespace models;

use dektrium\user\models\User;
use yii\codeception\TestCase;

class RegisterableTest extends TestCase
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
		// todo: fix here
		// test is failing without following line
		\Yii::$app->getModule('user');

		$user = new User(['scenario' => 'register']);
		$user->setAttributes([
			'username' => 'tester2',
			'email' => 'tester2@example.com',
		]);
		$this->assertFalse($user->register());
		$this->assertTrue($user->register(true));
	}
}