<?php

namespace dektrium\user\tests\unit;

use Codeception\Specify;
use \dektrium\user\Factory;
use \yii\codeception\TestCase;

class FactoryTest extends TestCase
{
	use Specify;

	/**
	 * @var Factory
	 */
	protected $factory;

	public function setUp()
	{
		parent::setUp();
		$this->factory = new Factory();
	}

	public function testCreateModels()
	{
		$this->specify('should create new user instance', function () {
			$user = $this->factory->createUser();
			$this->assertInstanceOf($this->factory->userClass, $user);
		});

		$this->specify('should create new user instance with params passed to constructor', function () {
			$user = $this->factory->createUser(['scenario' => 'register']);
			$this->assertInstanceOf($this->factory->userClass, $user);
			$this->assertEquals('register', $user->scenario);
		});

		$this->specify('should create new profile instance', function () {
			$user = $this->factory->createProfile();
			$this->assertInstanceOf($this->factory->profileClass, $user);
		});

		$this->specify('should create new profile instance with params passed to constructor', function () {
			$user = $this->factory->createProfile(['scenario' => 'update']);
			$this->assertInstanceOf($this->factory->profileClass, $user);
			$this->assertEquals('update', $user->scenario);
		});
	}

	public function testCreateQueries()
	{
		$this->specify('should create user query', function () {
			$query = $this->factory->createUserQuery();
			$this->assertInstanceOf($this->factory->userQueryClass, $query);
		});

		$this->specify('should create profile query', function () {
			$query = $this->factory->createProfileQuery();
			$this->assertInstanceOf($this->factory->profileQueryClass, $query);
		});
	}

	public function testCreateForms()
	{
		$this->specify('should create new resend form', function () {
			$model = $this->factory->createForm('resend');
			$this->assertInstanceOf($this->factory->resendFormClass, $model);
		});

		$this->specify('should create new login form', function () {
			$model = $this->factory->createForm('login');
			$this->assertInstanceOf($this->factory->loginFormClass, $model);
		});

		$this->specify('should create new recovery form', function () {
			$model = $this->factory->createForm('recovery');
			$this->assertInstanceOf($this->factory->recoveryFormClass, $model);
		});

		$this->specify('should create new recovery form with params passed to constructor', function () {
			$model = $this->factory->createForm('recovery', ['scenario' => 'register']);
			$this->assertInstanceOf($this->factory->recoveryFormClass, $model);
			$this->assertEquals('register', $model->scenario);
		});
	}
}