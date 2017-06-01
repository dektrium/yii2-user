<?php

namespace tests\_pages;

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
