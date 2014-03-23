<?php

namespace dektrium\user\tests\unit;

use Codeception\Specify;
use dektrium\user\ModelManager;
use dektrium\user\tests\_fixtures\UserFixture;

class ModelManagerTest extends TestCase
{
    use Specify;

    /**
     * @var ModelManager
     */
    protected $manager;

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
        $this->manager = new ModelManager();
    }

    public function testCreatingModels()
    {
        $this->specify('should create new user instance', function () {
            $user = $this->manager->createUser();
            $this->assertInstanceOf($this->manager->userClass, $user);
        });

        $this->specify('should create new user instance with params passed to constructor', function () {
            $user = $this->manager->createUser(['scenario' => 'register']);
            $this->assertInstanceOf($this->manager->userClass, $user);
            $this->assertEquals('register', $user->scenario);
        });

        $this->specify('should create new profile instance', function () {
            $user = $this->manager->createProfile();
            $this->assertInstanceOf($this->manager->profileClass, $user);
        });

        $this->specify('should create new profile instance with params passed to constructor', function () {
            $user = $this->manager->createProfile(['scenario' => 'update']);
            $this->assertInstanceOf($this->manager->profileClass, $user);
            $this->assertEquals('update', $user->scenario);
        });
    }

    public function testCreatingQueries()
    {
        $this->specify('should create user query', function () {
            $query = $this->manager->createUserQuery();
            $this->assertInstanceOf($this->manager->userQueryClass, $query);
        });

        $this->specify('should create profile query', function () {
            $query = $this->manager->createProfileQuery();
            $this->assertInstanceOf($this->manager->profileQueryClass, $query);
        });
    }

    public function testCreatingForms()
    {
        $this->specify('should create new resend form', function () {
            $model = $this->manager->createResendForm();
            $this->assertInstanceOf($this->manager->resendFormClass, $model);
        });

        $this->specify('should create new login form', function () {
            $model = $this->manager->createLoginForm();
            $this->assertInstanceOf($this->manager->loginFormClass, $model);
        });

        $this->specify('should create new password recovery request form', function () {
            $model = $this->manager->createPasswordRecoveryRequestForm();
            $this->assertInstanceOf($this->manager->passwordRecoveryRequestFormClass, $model);
        });

        $this->specify('should create new password recovery form', function () {
            $model = $this->manager->createPasswordRecoveryForm([
                'id' => 6,
                'token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6'
            ]);
            $this->assertInstanceOf($this->manager->passwordRecoveryFormClass, $model);
        });
    }

    public function testFindingUsers()
    {
        $this->specify('should find user by username', function () {
            $user = $this->manager->findUserByUsername('user');
            verify($user->username)->equals('user');
            verify($user->email)->equals('user@example.com');
        });

        $this->specify('should find user by email', function () {
            $user = $this->manager->findUserByEmail('user@example.com');
            verify($user->username)->equals('user');
            verify($user->email)->equals('user@example.com');
        });

        $this->specify('should find user by email or username', function () {
            $user = $this->manager->findUserByUsernameOrEmail('user');
            verify($user->username)->equals('user');
            verify($user->email)->equals('user@example.com');
            $user = $this->manager->findUserByUsernameOrEmail('user@example.com');
            verify($user->username)->equals('user');
            verify($user->email)->equals('user@example.com');
        });

        $this->specify('should find user by confirmation token', function () {
            $user = $this->manager->findUserByIdAndConfirmationToken(2, 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6');
            verify($user->username)->equals('unconfirmed');
        });

        $this->specify('should find user by recovery token', function () {
            $user = $this->manager->findUserByIdAndRecoveryToken(5, 'dghFKJA6JvjTKLAwyE5w2XD9b2lmBXLE');
            verify($user->username)->equals('andrew');
        });
    }
}