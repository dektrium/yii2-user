<?php

use \dektrium\user\Factory;
use \yii\codeception\TestCase;

class FactoryTest extends TestCase
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
		$scenario = 'register';
		$user = $this->factory->createUser(['scenario' => $scenario]);
		$this->assertInstanceOf($this->factory->modelClass, $user);
		$this->assertEquals($scenario, $user->scenario);
	}

	public function testCreateQuery()
	{
		$query = $this->factory->createQuery();
		$this->assertInstanceOf($this->factory->queryClass, $query);
	}

	public function testCreateResendForm()
	{
		$model = $this->factory->createForm('resend');
		$this->assertInstanceOf($this->factory->resendFormClass, $model);
	}

	public function testCreateLoginForm()
	{
		$model = $this->factory->createForm('login');
		$this->assertInstanceOf($this->factory->loginFormClass, $model);
	}

	public function testCreateRecoveryForm()
	{
		$scenario = 'register';
		$model = $this->factory->createForm('recovery', ['scenario' => $scenario]);
		$this->assertInstanceOf($this->factory->recoveryFormClass, $model);
		$this->assertEquals($scenario, $model->scenario);
	}
}