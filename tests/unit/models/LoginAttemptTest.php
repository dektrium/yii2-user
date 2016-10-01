<?php

namespace tests\unit\models;

use dektrium\user\models\LoginAttempt;
use Codeception\Specify;
use tests\_fixtures\LoginAttemptFixture;

/**
 * Test the login attempt model.
 */
class LoginAttemptTest extends \Codeception\Test\Unit
{
    use Specify;
    
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    /**
     * Test getLoginLockTime method.
     */
    public function testGetLoginLockTime()
    {
        $this->specify('lockTime > 0', function () {
            $this->tester->haveFixtures(['login_attempt' => LoginAttemptFixture::className()]);
            $fixture = $this->tester->grabFixture('login_attempt', 'test');
            $model = LoginAttempt::find()->where(['ip' => $fixture->ip])->one();
            $this->assertTrue($model->attempts == $fixture->attempts);
            $this->assertTrue($model->getLoginLockTime() > 0);
        });
        
        $this->specify('lockTime is 0', function () {
            $this->tester->haveFixtures(['login_attempt' => LoginAttemptFixture::className()]);
            $fixture = $this->tester->grabFixture('login_attempt', 'lockTime0');
            $model = LoginAttempt::find()->where(['ip' => $fixture->ip])->one();
            $this->assertTrue($model->attempts == $fixture->attempts);
            $this->assertTrue($model->getLoginLockTime() == 0);
        });
    }

    /**
     * Test purgeOld method.
     */
    public function testPurgeOld()
    {
        $this->assertTrue(LoginAttempt::purgeOld() >= 0);
    }
    
    /**
     * Test removeByIp method.
     */
    public function testRemoveByIp()
    {
        $this->tester->haveFixtures(['login_attempt' => LoginAttemptFixture::className()]);
        $fixture = $this->tester->grabFixture('login_attempt', 'test');
        $item = $this->tester->grabRecord(LoginAttempt::className(), ['ip' => $fixture->ip]);
        $this->assertTrue(LoginAttempt::removeByIp($fixture->ip) >= 0);
        $this->tester->dontSeeRecord(LoginAttempt::className(), ['ip' => $fixture->ip]);
    }
}
