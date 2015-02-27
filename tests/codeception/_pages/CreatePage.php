<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents admin create page.
 *
 * @property \FunctionalTester $actor
 */
class CreatePage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/admin/create';

    /**
     * @param $username
     * @param $email
     * @param $password
     */
    public function create($username, $email, $password)
    {
        $this->actor->fillField('#user-username', $username);
        $this->actor->fillField('#user-email', $email);
        $this->actor->fillField('#user-password', $password);
        $this->actor->click('Save');
    }
}
