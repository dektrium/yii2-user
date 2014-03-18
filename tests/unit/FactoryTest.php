<?php

namespace dektrium\user\tests\unit;

use Codeception\Specify;
use \dektrium\user\Factory;
use dektrium\user\tests\_fixtures\UserFixture;
use \yii\codeception\TestCase;

class FactoryTest extends TestCase
{
    use Specify;

    /**
     * @var Factory
     */
    protected $factory;

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

        $this->specify('should create new password recovery request form', function () {
            $model = $this->factory->createForm('passwordRecoveryRequest');
            $this->assertInstanceOf($this->factory->passwordRecoveryRequestFormClass, $model);
        });

        $this->specify('should create new password recovery form', function () {
            $model = $this->factory->createForm('passwordRecovery', [
                'id' => 6,
                'token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6'
            ]);
            $this->assertInstanceOf($this->factory->passwordRecoveryFormClass, $model);
        });
    }
}
