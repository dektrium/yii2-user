<?php

namespace dektrium\user\tests;

use Codeception\Specify;
use dektrium\user\models\RecoveryForm;
use tests\codeception\fixtures\UserFixture;
use tests\codeception\fixtures\TokenFixture;
use yii\codeception\TestCase;
use yii\base\InvalidParamException;

class RecoveryFormTest extends TestCase
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
                'dataFile' => '@tests/codeception/fixtures/data/init_user.php'
            ],
            'token' => [
                'class' => TokenFixture::className(),
                'dataFile' => '@tests/codeception/fixtures/data/init_token.php'
            ]
        ];
    }

    public function testFormCreation()
    {
        $this->specify('should accept id and token on creation', function () {
            $token = $this->getFixture('token')->getModel('recovery');
            $form = new RecoveryForm(['token' => $token]);
            verify($form instanceof RecoveryForm)->true();
        });

        $this->specify('should throw exception if token is null', function () {
            try {
                $form = new RecoveryForm([
                    'token' => null]);
            } catch (\Exception $e) {
                verify($e instanceof \RuntimeException)->true();
                verify($e->getMessage())->equals('Token should be passed to config');
            }
        });

        $this->specify('should throw exception if recovery token expired', function () {
            try {
                $token = $this->getFixture('token')->getModel('expired_recovery');
                $form = new RecoveryForm(['token' => $token]);
            } catch (\Exception $e) {
                verify($e instanceof InvalidParamException)->true();
                verify($e->getMessage())->equals('Invalid token');
            }
        });
    }

    public function testFormValidation()
    {
        $token = $this->getFixture('token')->getModel('recovery');
        $form = new RecoveryForm(['token' => $token]);
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
