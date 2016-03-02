<?php

namespace tests\codeception\_support;

use AspectMock\Test;
use Codeception\Module;
use Codeception\TestCase;

class CodeHelper extends Module
{
    /**
     * @param TestCase $testcase
     */
    public function _after(TestCase $testcase)
    {
        Test::clean();
        parent::_after($testcase);
    }
}
