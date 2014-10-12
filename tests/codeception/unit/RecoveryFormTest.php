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

    public function testFormValidation()
    {
        $form = \Yii::createObject(RecoveryForm::className());
        $form->scenario = 'reset';
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
