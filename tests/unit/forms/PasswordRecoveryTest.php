<?php

namespace dektrium\user\tests\forms;

use Codeception\Specify;
use dektrium\user\forms\PasswordRecovery;
use dektrium\user\tests\_fixtures\UserFixture;
use dektrium\user\tests\unit\TestCase;
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
            $form = new PasswordRecovery(['id' => 6, 'token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
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
                $form = new PasswordRecovery(['id' => 5, 'token' => 'dghFKJA6JvjTKLAwyE5w2XD9b2lmBXLE']);
            } catch (\Exception $e) {
                verify($e instanceof InvalidParamException)->true();
                verify($e->getMessage())->equals('Token has been expired');
            }
        });
    }

    public function testFormValidation()
    {
        $form = new PasswordRecovery(['id' => 6, 'token' => 'NO2aCmBIjFQX624xmAc3VBu7Th3NJoa6']);
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
