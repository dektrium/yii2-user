<?php namespace forms;

use dektrium\user\forms\Registration;
use yii\codeception\TestCase;

class RegistrationTest extends TestCase
{
	public function testPasswordValidation()
	{
		$form = new Registration();
		$form->setAttributes([
			'email' => 'foobar@example.com',
			'username' => 'foobar',
		]);
		$this->assertFalse($form->validate());
		$form->setAttributes(['password' => 'abcd']);
		$this->assertFalse($form->validate());
		$form->setAttributes(['password' => 'abcdef']);
		$this->assertTrue($form->validate());
		\Yii::$app->getModule('user')->generatePassword = true;
		$form = new Registration();
		$form->setAttributes([
			'email' => 'foobar@example.com',
			'username' => 'foobar',
		]);
		$this->assertTrue($form->validate());
	}

	public function testCaptcha()
	{
		$form = new Registration();
		$form->setAttributes([
			'email' => 'foobar@example.com',
			'username' => 'foobar',
			'password' => 'foobar'
		]);
		$this->assertTrue($form->validate());
		\Yii::$app->getModule('user')->captcha[] = 'register';
		$form = new Registration();
		$form->setAttributes([
			'email' => 'foobar@example.com',
			'username' => 'foobar',
			'password' => 'foobar'
		]);
		$this->assertFalse($form->validate());
	}
}