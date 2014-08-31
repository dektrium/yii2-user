<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents admin page.
 *
 * @property \FunctionalTester $actor
 */
class AdminPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/admin/index';
}
