<?php

namespace dektrium\user\tests\unit;

use Codeception\Specify;
use dektrium\user\ModelManager;
use tests\codeception\fixtures\UserFixture;
use tests\codeception\fixtures\TokenFixture;
use yii\codeception\TestCase;

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
                'dataFile' => '@tests/codeception/fixtures/data/init_user.php'
            ],
            'token' => [
                'class' => TokenFixture::className(),
                'dataFile' => '@tests/codeception/fixtures/data/init_token.php'
            ]
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
            $this->assertInstanceOf('\yii\db\Query', $query);
        });

        $this->specify('should create profile query', function () {
            $query = $this->manager->createProfileQuery();
            $this->assertInstanceOf('\yii\db\Query', $query);
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
            $model = $this->manager->createRecoveryRequestForm();
            $this->assertInstanceOf($this->manager->recoveryRequestFormClass, $model);
        });

        $this->specify('should create new password recovery form', function () {
            $token = $this->getFixture('token')->getModel('recovery');
            $model = $this->manager->createRecoveryForm([
                'token' => $token
            ]);
            $this->assertInstanceOf($this->manager->recoveryFormClass, $model);
        });
    }

    public function testFindingUsers()
    {
        $this->specify('should find user by username', function () {
            $expected = $this->getFixture('user')->getModel('user');
            $user = $this->manager->findUserByUsername($expected->username);
            verify($user->username)->equals($expected->username);
            verify($user->email)->equals($expected->email);
        });

        $this->specify('should find user by email', function () {
            $expected = $this->getFixture('user')->getModel('user');
            $user = $this->manager->findUserByEmail($expected->email);
            verify($user->username)->equals($expected->username);
            verify($user->email)->equals($expected->email);
        });

        $this->specify('should find user by email or username', function () {
            $expected = $this->getFixture('user')->getModel('user');
            $user = $this->manager->findUserByUsernameOrEmail($expected->username);
            verify($user->username)->equals($expected->username);
            verify($user->email)->equals($expected->email);
            $user = $this->manager->findUserByUsernameOrEmail($expected->email);
            verify($user->username)->equals($expected->username);
            verify($user->email)->equals($expected->email);
        });
    }
}