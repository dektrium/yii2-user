<?php

namespace tests\codeception\_support;

use Codeception\Module;
use Codeception\TestCase;
use tests\codeception\_fixtures\ProfileFixture;
use tests\codeception\_fixtures\TokenFixture;
use tests\codeception\_fixtures\UserFixture;
use yii\test\FixtureTrait;

class FixtureHelper extends Module
{
    use FixtureTrait;

    /**
     * @var array
     */
    public static $excludeActions = ['loadFixtures', 'unloadFixtures', 'getFixtures', 'globalFixtures', 'fixtures'];

    /**
     * @param TestCase $testcase
     */
    public function _before(TestCase $testcase)
    {
        $this->unloadFixtures();
        $this->loadFixtures();
        parent::_before($testcase);
    }

    /**
     * @param TestCase $testcase
     */
    public function _after(TestCase $testcase)
    {
        $this->unloadFixtures();
        parent::_after($testcase);
    }

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class'    => UserFixture::className(),
                'dataFile' => '@tests/codeception/_fixtures/data/init_user.php',
            ],
            'token' => [
                'class'    => TokenFixture::className(),
                'dataFile' => '@tests/codeception/_fixtures/data/init_token.php',
            ],
            'profile' => [
                'class'    => ProfileFixture::className(),
                'dataFile' => '@tests/codeception/_fixtures/data/init_profile.php',
            ],
        ];
    }
}
