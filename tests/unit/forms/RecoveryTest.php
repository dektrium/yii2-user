<?php

namespace dektrium\user\tests\forms;

use dektrium\user\forms\Recovery;
use dektrium\user\tests\_fixtures\UserFixture;
use yii\codeception\TestCase;

class RecoveryTest extends TestCase
{
	/**
	 * @inheritdoc
	 */
	public function fixtures()
	{
		return [
			'user' => [
				'class' => UserFixture::className(),
				'dataFile' => '@tests/_fixtures/init_user.php'
			],
		];
	}

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