<?php

namespace dektrium\user\tests\models;

use Codeception\Specify;
use dektrium\user\models\Profile;
use dektrium\user\models\User;
use dektrium\user\tests\_fixtures\UserFixture;
use dektrium\user\tests\unit\TestCase;
use yii\helpers\Html;
use yii\helpers\Security;

/**
 * Test suite for User active record class.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserTest extends TestCase
{
    use Specify;

    /**
     * @var User
     */
    protected $user;

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

    public function testValidation()
    {
        \Yii::$app->getModule('user');
        $this->user = new User(['scenario' => 'register']);

        $this->specify('username is valid', function () {
            verify('username is required', $this->user->validate(['username']))->false();
            $this->user->username = Security::generateRandomKey();
            verify('username is too long', $this->user->validate(['username']))->false();
            $this->user->username = '!@# абв';
            verify('username contains invalid characters', $this->user->validate(['username']))->false();
            $this->user->username = 'user';
            verify('username is already using', $this->user->validate(['username']))->false();
            $this->user->username = 'perfect_name';
            verify('username is ok', $this->user->validate(['username']))->true();
        });

        $this->specify('email is valid', function () {
            verify('email is required', $this->user->validate(['email']))->false();
            $this->user->email = 'not valid email';
            verify('email is not email', $this->user->validate(['email']))->false();
            $this->user->email = 'user@example.com';
            verify('email is already using', $this->user->validate(['email']))->false();
            $this->user->email = 'perfect@example.com';
            verify('email is ok', $this->user->validate(['email']))->true();
        });

        $this->specify('password is valid', function () {
            verify('password is required', $this->user->validate(['password']))->false();
            $this->user->password = '12345';
            verify('password is too short', $this->user->validate(['password']))->false();
            $this->user->password = 'superSecretPa$$word';
            verify('password is ok', $this->user->validate(['password']))->true();
        });
    }

    public function testRegister()
    {
        $this->specify('user should be registered', function () {
            $user = new User(['scenario' => 'register']);
            $user->username = 'tester';
            $user->email = 'tester@example.com';
            $user->password = 'tester';
            verify($user->register())->true();
            verify($user->username)->equals('tester');
            verify($user->email)->equals('tester@example.com');
            verify(Security::validatePassword('tester', $user->password_hash))->true();
        });

        $this->specify('user should be registered without password if special option is set', function () {
            $user = new User(['scenario' => 'short_register']);
            $user->username = 'another_tester';
            $user->email = 'another_tester@example.com';
            verify($user->register())->true();
            $email = $this->getLastMessage();
            $this->assertEmailIsSent();
            $this->assertEmailRecipientsContain('<another_tester@example.com>', $email);
            $this->assertEmailSubjectContains('Welcome to', $email);
            $this->assertEmailHtmlContains($user->password, $email);
            verify($user->username)->equals('another_tester');
            verify($user->email)->equals('another_tester@example.com');
            verify($user->password)->notNull();
            verify($user->password_hash)->notNull();
            verify(Security::validatePassword($user->password, $user->password_hash))->true();
        });

        $this->specify('profile should be created after registration', function () {
            $user = new User(['scenario' => 'register']);
            $user->username = 'john_doe';
            $user->email = 'john_doe@example.com';
            $user->password = 'qwerty';
            verify($user->register())->true();
            verify($user->profile->gravatar_email)->equals('john_doe@example.com');
        });
    }

    public function testBlocking()
    {
        $this->specify('user can be blocked and unblocked', function () {
            $user = User::find(1);
            verify('user is not blocked', $user->getIsBlocked())->false();
            $user->block();
            verify('user is blocked', $user->getIsBlocked())->true();
            $user->unblock();
            verify('user is unblocked', $user->getIsBlocked())->false();
        });
    }

    public function testConfirmable()
    {
        \Yii::$app->getModule('user')->confirmable = true;

        $this->user = new User(['scenario' => 'register']);
        $this->user->username = 'tester';
        $this->user->email = 'tester@example.com';
        $this->user->password = 'tester';
        $this->user->register();

        $this->specify('confirmation data should be generated on register', function () {
            verify($this->user->confirmation_token)->notNull();
            verify($this->user->confirmation_sent_at)->notNull();
        });

        $this->specify('confirmation message should be sent on register', function () {
            $email = $this->getLastMessage();
            $this->assertEmailIsSent();
            $this->assertEmailRecipientsContain('<tester@example.com>', $email);
            $this->assertEmailSubjectContains('Please confirm your account', $email);
            $this->assertEmailHtmlContains(Html::encode($this->user->getConfirmationUrl()), $email);
        });

        $this->specify('correct user confirmation status should be returned', function () {
            $user = User::find(1);
            verify('user is confirmed', $user->getIsConfirmed())->true();
            $user = User::find(2);
            verify('user is not confirmed', $user->getIsConfirmed())->false();
        });

        $this->specify('correct user confirmation url should be returned', function () {
            $user = User::find(1);
            verify('url is null for confirmed user', $user->getConfirmationUrl())->null();
            $user = User::find(2);
            $needle = \Yii::$app->getUrlManager()->createAbsoluteUrl(['/user/registration/confirm',
                'id' => $user->id,
                'token' => $user->confirmation_token
            ]);
            verify('url contains correct id and confirmation token for unconfirmed user', $user->getConfirmationUrl())->contains($needle);
        });

        $this->specify('confirmation token should become invalid after specified time', function () {
            \Yii::$app->getModule('user')->confirmWithin = $expirationTime = 86400;
            $user = new User([
                'confirmation_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
                'confirmation_sent_at' => time()
            ]);
            verify($user->getIsConfirmationPeriodExpired())->false();
            $user = new User([
                'confirmation_token'   => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
                'confirmation_sent_at' => time() - $expirationTime - 1
            ]);
            verify($user->getIsConfirmationPeriodExpired())->true();
        });

        $this->specify('user should be confirmed by updating confirmed_at field', function () {
            $user = User::find(2);
            verify($user->confirmed_at)->null();
            $user->confirm();
            verify($user->confirmed_at)->notNull();
        });
    }

    public function testEmailSettings()
    {
        $this->user = User::find(1);
        $this->user->scenario = 'update_email';
        $this->user->unconfirmed_email = 'new_email@example.com';
        $this->user->current_password = 'qwerty';
        $this->user->updateEmail();

        $this->specify('email should be updated', function () {
            verify($this->user->email)->equals('new_email@example.com');
            verify($this->user->unconfirmed_email)->null();
        });

        \Yii::$app->getModule('user')->confirmable = true;

        $this->specify('confirmation message should be sent if confirmable is enabled', function () {
            $this->user->unconfirmed_email = 'another_email@example.com';
            $this->user->current_password = 'qwerty';
            $this->user->updateEmail();
            verify($this->user->email)->equals('new_email@example.com');
            verify($this->user->unconfirmed_email)->equals('another_email@example.com');
            $email = $this->getLastMessage();
            $this->assertEmailIsSent();
            $this->assertEmailRecipientsContain('<another_email@example.com>', $email);
            $this->assertEmailSubjectContains('Please confirm your email', $email);
            $this->assertEmailHtmlContains(Html::encode($this->user->getConfirmationUrl()), $email);
        });

        $this->specify('email reconfirmation should be reset', function () {
            $this->user->resetEmailUpdate();
            verify($this->user->email)->equals('new_email@example.com');
            verify($this->user->unconfirmed_email)->null();
            verify($this->user->confirmation_sent_at)->null();
            verify($this->user->confirmation_token)->null();
        });
    }

    public function testRecoverable()
    {
        \Yii::$app->getModule('user')->recoverable = true;
        $this->user = User::find(1);
        $this->user->sendRecoveryMessage();

        $this->specify('recovery message should be sent', function () {
            $email = $this->getLastMessage();
            $this->assertEmailIsSent();
            $this->assertEmailRecipientsContain('<user@example.com>', $email);
            $this->assertEmailSubjectContains('Please complete password reset', $email);
            $this->assertEmailHtmlContains(Html::encode($this->user->getRecoveryUrl()), $email);
        });

        $this->specify('correct user confirmation url should be returned', function () {
            $needle = \Yii::$app->getUrlManager()->createAbsoluteUrl(['/user/recovery/reset',
                'id' => $this->user->id,
                'token' => $this->user->recovery_token
            ]);
            verify($this->user->getRecoveryUrl())->contains($needle);
        });

        $this->specify('confirmation token should become invalid after specified time', function () {
            \Yii::$app->getModule('user')->recoverWithin = $expirationTime = 86400;
            $user = new User([
                'recovery_token' => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
                'recovery_sent_at' => time()
            ]);
            verify($user->getIsRecoveryPeriodExpired())->false();
            $user = new User([
                'recovery_token'   => 'NNWJf_CoV8ocX3AsYK38CoOGkXUcpQK4',
                'recovery_sent_at' => time() - $expirationTime - 1
            ]);
            verify($user->getIsRecoveryPeriodExpired())->true();
        });
    }
}
