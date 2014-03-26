<?php

namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use dektrium\user\tests\_fixtures\ProfileFixture;
use dektrium\user\tests\_fixtures\UserFixture;
use Codeception\TestCase;
use yii\test\FixtureTrait;

class TestHelper extends \Codeception\Module
{
    /**
     * Redeclare visibility because codeception includes all public methods that not starts from "_"
     * and not excluded by module settings, in guy class.
     */
    use FixtureTrait {
        loadFixtures as protected;
        fixtures as protected;
        globalFixtures as protected;
        unloadFixtures as protected;
        getFixtures as protected;
    }

    public function _before(TestCase $test)
    {
        $this->loadFixtures();
        parent::_before($test);
    }

    public function _after(TestCase $test)
    {
        $this->unloadFixtures();
        parent::_after($test);
    }

    protected function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/_fixtures/init_user.php'
            ],
            'profile' => [
                'class' => ProfileFixture::className(),
                'dataFile' => '@tests/_fixtures/init_profile.php'
            ],
        ];
    }
}