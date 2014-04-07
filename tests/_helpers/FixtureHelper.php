<?php

namespace Codeception\Module;

use Codeception\Module;
use Codeception\TestCase;
use yii\test\FixtureTrait;

class FixtureHelper extends Module
{
    use FixtureTrait;

    /**
     * @var array
     */
    public static $excludeActions = ['loadFixtures', 'unloadFixtures', 'getFixtures', 'globalFixtures', 'fixtures'];

    /**
     * @var array
     */
    protected $config = ['fixtures'];

    /**
     * @var array
     */
    protected $requiredFields = ['fixtures'];

    /**
     * @var array
     */
    protected $fixtures = [];

    /**
     * Used after configuration is loaded
     */
    public function _initialize() {
        foreach ($this->config['fixtures'] as $name => $fixture) {
            $this->fixtures[$name] = [
                'class'    => $fixture['class'],
                'dataFile' => $fixture['dataFile']
            ];
        }
    }

    /**
     * @param TestCase $test
     */
    public function _before(TestCase $test)
    {
        $this->unloadFixtures();
        $this->loadFixtures();
        parent::_before($test);
    }

    /**
     * @param TestCase $test
     */
    public function _after(TestCase $test)
    {
        $this->unloadFixtures();
        parent::_after($test);
    }

    /**
     * @return array
     */
    public function fixtures()
    {
        return $this->fixtures;
    }
}
