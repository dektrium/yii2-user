<?php

namespace tests\codeception\_support;

use Codeception\Module;
use Codeception\TestCase\Cept;
use tests\codeception\fixtures\ProfileFixture;
use tests\codeception\fixtures\TokenFixture;
use tests\codeception\fixtures\UserFixture;
use yii\test\FixtureTrait;

class FixtureHelper extends Module
{
    use FixtureTrait;

    /**
     * @var array
     */
    public static $excludeActions = ['loadFixtures', 'unloadFixtures', 'getFixtures', 'globalFixtures', 'fixtures'];

    /**
     * @param Cept $cept
     */
    public function _before(Cept $cept)
    {
        $this->unloadFixtures();
        $this->loadFixtures();
        parent::_before($cept);
    }

    /**
     * @param Cept $cept
     */
    public function _after(Cept $cept)
    {
        $this->unloadFixtures();
        parent::_after($cept);
    }

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class'    => UserFixture::className(),
                'dataFile' => '@tests/codeception/fixtures/data/init_user.php',
            ],
            'token' => [
                'class'    => TokenFixture::className(),
                'dataFile' => '@tests/codeception/fixtures/data/init_token.php',
            ],
            'profile' => [
                'class'    => ProfileFixture::className(),
                'dataFile' => '@tests/codeception/fixtures/data/init_profile.php',
            ],
        ];
    }
}
