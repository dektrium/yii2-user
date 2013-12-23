<?php namespace forms;

use dektrium\user\forms\Resend;
use yii\codeception\TestCase;

class ResendTest extends TestCase
{
	public function testValidateEmail()
	{
		$form = new Resend();
		$form->setAttributes([
			'email' => 'user@example.com',
		]);
		$this->assertFalse($form->validate());

		$form = new Resend();
		$form->setAttributes([
			'email' => 'unconfirmed@example.com',
		]);
		$this->assertTrue($form->validate());
	}

	public function testCaptcha()
	{
		$form = new Resend();
		$form->setAttributes([
			'email' => 'unconfirmed@example.com',
		]);
		$this->assertTrue($form->validate());

		\Yii::$app->getModule('user')->captcha[] = 'resend';
		$form = new Resend();
		$form->setAttributes([
			'email' => 'unconfirmed@example.com',
		]);
		$this->assertFalse($form->validate());
	}
}