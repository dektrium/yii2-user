<?php

namespace dektrium\user\tests\forms;

use Codeception\Specify;
use dektrium\user\forms\PasswordRecovery;
use dektrium\user\tests\_fixtures\UserFixture;
use yii\codeception\TestCase;
use yii\base\InvalidParamException;

class PasswordRecoveryTest extends TestCase
{
    use Specify;

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

    public function testFormCreation()
    {
        $this->specify('should accept id and token on creation', function () {
            $user = $this->getFixture('user')->getModel('user_with_recovery_token');
            $form = new PasswordRecovery(['id' => $user->id, 'token' => $user->recovery_token]);
            verify($form instanceof PasswordRecovery)->true();
        });

        $this->specify('should throw exception if user not found', function () {
            try {
                $form = new PasswordRecovery(['id' => 42, 'token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
            } catch (\Exception $e) {
                verify($e instanceof InvalidParamException)->true();
                verify($e->getMessage())->equals('Wrong password reset token');
            }
        });

        $this->specify('should throw exception if recovery token expired', function () {
            try {
                $user = $this->getFixture('user')->getModel('user_with_expired_recovery_token');
                $form = new PasswordRecovery(['id' => $user->id, 'token' => $user->recovery_token]);
            } catch (\Exception $e) {
                verify($e instanceof InvalidParamException)->true();
                verify($e->getMessage())->equals('Token has been expired');
            }
        });
    }

    public function testFormValidation()
    {
        $user = $this->getFixture('user')->getModel('user_with_recovery_token');
        $form = new PasswordRecovery(['id' => $user->id, 'token' => $user->recovery_token]);
        $this->specify('password is required', function () use ($form) {
            verify($form->validate(['password']))->false();
        });
        $this->specify('password is too short', function () use ($form) {
            $form->password = '12345';
            verify($form->validate(['password']))->false();
        });
        $this->specify('password is ok', function () use ($form) {
            $form->password = 'superSecretPa$$word';
            verify($form->validate(['password']))->true();
        });
    }
}
