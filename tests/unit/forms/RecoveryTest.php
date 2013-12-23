<?php namespace forms;

use dektrium\user\forms\Recovery;
use yii\codeception\TestCase;

class RecoveryTest extends TestCase
{
	public function testValidateUserConfirmed()
	{
		$form = new Recovery(['scenario' => 'request']);
		$form->setAttributes([
			'email' => 'user@example.com',
		]);
		$this->assertTrue($form->validate());
		$form = new Recovery(['scenario' => 'request']);
		$form->setAttributes([
			'email' => 'unconfirmed@example.com',
		]);
		$this->assertFalse($form->validate());
	}

	public function testCaptcha()
	{
		$form = new Recovery(['scenario' => 'request']);
		$form->setAttributes([
			'email' => 'user@example.com',
		]);
		$this->assertTrue($form->validate());

		\Yii::$app->getModule('user')->captcha[] = 'recovery';
		$form = new Recovery(['scenario' => 'request']);
		$form->setAttributes([
			'email' => 'user@example.com',
		]);
		$this->assertFalse($form->validate());
	}
}