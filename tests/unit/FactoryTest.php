<?php

use \dektrium\user\Factory;
use \Codeception\TestCase\Test;

class FactoryTest extends Test
{
	/**
	 * @var Factory
	 */
	protected $factory;

	public function setUp()
	{
		parent::setUp();
		$this->factory = new Factory();
	}

	public function testCreateUser()
	{
		$user = $this->factory->createUser();
		$this->assertInstanceOf('\dektrium\user\models\User', $user);
	}

	public function testCreateQuery()
	{
		$query = $this->factory->createQuery();
		$this->assertInstanceOf('\yii\db\ActiveQuery', $query);
	}

	public function testCreateRegistrationForm()
	{
		$model = $this->factory->createForm('registration');
		$this->assertInstanceOf('\dektrium\user\forms\Registration', $model);
	}

	public function testCreateResendForm()
	{
		$model = $this->factory->createForm('resend');
		$this->assertInstanceOf('\dektrium\user\forms\Resend', $model);
	}

	public function testCreateLoginForm()
	{
		$model = $this->factory->createForm('login');
		$this->assertInstanceOf('\dektrium\user\forms\Login', $model);
	}

	public function testCreateRecoveryForm()
	{
		$model = $this->factory->createForm('recovery');
		$this->assertInstanceOf('\dektrium\user\forms\Recovery', $model);
	}
}