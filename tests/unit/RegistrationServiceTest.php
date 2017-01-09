<?php


use Codeception\Util\Stub;
use dektrium\user\helpers\PasswordGenerator;
use dektrium\user\Mailer;
use dektrium\user\models\Profile;
use dektrium\user\models\RegistrationForm;
use dektrium\user\models\User;
use dektrium\user\service\RegistrationService;
use dektrium\user\service\UserConfirmation;
use yii\db\ActiveQuery;

class RegistrationServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $profile;

    protected $query;

    /**
     * @var User
     */
    protected $user;

    protected $form;

    protected function _before()
    {
        $this->profile = Stub::make(Profile::className(), [
            'attributes' => ['myAttribute'],
            'getAttributes' => Stub::atLeastOnce(function () {
                return ['myAttribute' => ''];
            }),
            'save' => Stub::atLeastOnce()
        ]);

        $this->query = Stub::make(ActiveQuery::className(), [
            'one' => Stub::atLeastOnce(function () {
                return $this->profile;
            }),
        ]);

        $this->user = Stub::make(User::className(), [
            'getProfile' => Stub::atLeastOnce(function () {
                return $this->query;
            }),
            'attributes' => [
                'username',
                'email',
                'password',
                'myAttribute',
                'confirmed_at',
            ],
            'getAttributes' => Stub::atLeastOnce(function () {
                return [
                    'username' => null,
                    'email' => null,
                    'password' => null,
                    'myAttribute' => null,
                    'confirmed_at' => null,
                ];
            }),
            'save' => Stub::atLeastOnce(),
        ]);

        $this->form = Stub::makeEmpty(RegistrationForm::className(), [
            'getMappings' => Stub::atLeastOnce(function () {
                return [];
            }),
            'password' => 'qwerty',
            'email' => 'tester@example.com',
            'username' => 'tester',
        ]);

        $confirmation = Stub::makeEmpty(UserConfirmation::className());
        Yii::$container->set(UserConfirmation::className(), $confirmation);

        Yii::$container->set(User::className(), $this->user);
    }

    protected function _after()
    {
    }

    public function testBaseAttributesAreLoaded()
    {
        /** @var RegistrationService $service */
        $service = Yii::createObject(RegistrationService::className());

        /** @var RegistrationForm $registrationForm */
        $registrationForm = Stub::makeEmpty(RegistrationForm::className(), [
            'getMappings' => Stub::atLeastOnce(function () {
                return [];
            }),
            'password' => 'qwerty',
            'email' => 'tester@example.com',
            'username' => 'tester',
        ]);

        $service->register($registrationForm);

        verify('email is loaded', $this->user->getEmail())->equals('tester@example.com');
        verify('username is loaded', $this->user->getUsername())->equals('tester');
        verify('password is loaded', $this->user->getPassword())->equals('qwerty');
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Email must not be null
     */
    public function testEmailMustNotBeEmpty()
    {
        /** @var RegistrationService $service */
        $service = Yii::createObject(RegistrationService::className());

        /** @var RegistrationForm $registrationForm */
        $registrationForm = Stub::makeEmpty(RegistrationForm::className(), [
            'getMappings' => Stub::atLeastOnce(function () {
                return [];
            }),
            'password' => 'qwerty',
            'email' => '',
            'username' => 'tester',
        ]);

        $service->register($registrationForm);
    }

    /**
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Password must not be null
     */
    public function testPasswordMustNotBeEmptyIfDisabledGeneration()
    {
        /** @var RegistrationService $service */
        $service = Yii::createObject(RegistrationService::className());

        /** @var RegistrationForm $registrationForm */
        $registrationForm = Stub::makeEmpty(RegistrationForm::className(), [
            'getMappings' => Stub::atLeastOnce(function () {
                return [];
            }),
            'password' => '',
            'email' => 'tester@example.com',
            'username' => 'tester',
        ]);

        $service->register($registrationForm);
    }

    public function testPasswordIsGenerated()
    {
        /** @var PasswordGenerator $generator */
        $generator = Stub::make(PasswordGenerator::className(), [
            'generate' => Stub::once(function () {
                return 'generatedPassword';
            }),
        ]);
        Yii::$container->set(PasswordGenerator::className(), $generator);
        /** @var RegistrationService $service */
        $service = Yii::createObject(RegistrationService::className());
        $service->isPasswordGenerated = true;

        /** @var RegistrationForm $registrationForm */
        $registrationForm = Stub::makeEmpty(RegistrationForm::className(), [
            'getMappings' => Stub::atLeastOnce(function () {
                return [];
            }),
            'password' => 'qwerty',
            'email' => 'tester@example.com',
            'username' => 'tester',
        ]);

        $service->register($registrationForm);
        verify('password is generated', $this->user->getPassword())->equals('generatedPassword');
    }

    public function testLoad()
    {
        /** @var RegistrationForm $registrationForm */
        /*$registrationForm = Stub::makeEmpty(RegistrationForm::className(), [
            'getMappings' => Stub::atLeastOnce(function () {
                return [
                    'userAttribute' => 'myAttribute',
                    'profileAttribute' => 'profile.myAttribute',
                ];
            }),
            'password' => 'qwerty',
            'email' => 'tester@example.com',
            'username' => 'tester',
            'userAttribute' => 'foobar',
            'profileAttribute' => 'foobar',
        ]);*/

        /** @var RegistrationService $service */
        /*$service = Yii::createObject(RegistrationService::className());
        $service->register($registrationForm);

        verify('userAttribute is set', $this->user->myAttribute)->equals('foobar');
        verify('profileAttribute is set', $this->user->profile->myAttribute)->equals('foobar');*/
    }
}