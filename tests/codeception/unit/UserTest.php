<?php

namespace dektrium\user\tests;

use Codeception\Specify;
use dektrium\user\models\User;
use tests\codeception\_fixtures\UserFixture;
use yii\codeception\TestCase;
use Yii;

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
                'dataFile' => '@tests/codeception/_fixtures/data/init_user.php'
            ],
        ];
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
            verify(Yii::$app->getSecurity()->validatePassword('tester', $user->password_hash))->true();
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
            $user = $this->getFixture('user')->getModel('user');
            verify('user is not blocked', $user->getIsBlocked())->false();
            $user->block();
            verify('user is blocked', $user->getIsBlocked())->true();
            $user->unblock();
            verify('user is unblocked', $user->getIsBlocked())->false();
        });
    }

    public function testenableConfirmation()
    {
        \Yii::$app->getModule('user')->enableConfirmation = true;

        $this->specify('should return correct user confirmation status', function () {
            $user = $this->getFixture('user')->getModel('user');
            verify('user is confirmed', $user->getIsConfirmed())->true();
            $user = $this->getFixture('user')->getModel('unconfirmed');
            verify('user is not confirmed', $user->getIsConfirmed())->false();
        });

        /*$this->specify('correct user confirmation url should be returned', function () {
            $user = User::findOne(1);
            verify('url is null for confirmed user', $user->getConfirmationUrl())->null();
            $user = User::findOne(2);
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
            $user = User::findOne(2);
            verify($user->confirmed_at)->null();
            $user->confirm();
            verify($user->confirmed_at)->notNull();
        });*/
    }

/*    public function testEmailSettings()
    {
        $this->user = User::findOne(1);
        $this->user->scenario = 'update_email';
        $this->user->unconfirmed_email = 'new_email@example.com';
        $this->user->current_password = 'qwerty';
        $this->user->updateEmail();

        $this->specify('email should be updated', function () {
            verify($this->user->email)->equals('new_email@example.com');
            verify($this->user->unconfirmed_email)->null();
        });

        \Yii::$app->getModule('user')->enableConfirmation = true;

        $this->specify('confirmation message should be sent if enableConfirmation is enabled', function () {
            $this->user->unconfirmed_email = 'another_email@example.com';
            $this->user->current_password = 'qwerty';
            $this->user->updateEmail();
            verify($this->user->email)->equals('new_email@example.com');
            verify($this->user->unconfirmed_email)->equals('another_email@example.com');
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
        $this->user = User::findOne(1);
        $this->user->sendRecoveryMessage();

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
    }*/
}
