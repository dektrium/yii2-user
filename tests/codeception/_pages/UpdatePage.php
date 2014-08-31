<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents admin update page.
 *
 * @property \FunctionalTester $actor
 */
class UpdatePage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/admin/update';

    /**
     * @param $username
     * @param $email
     * @param $password
     */
    public function update($username, $email, $password = null)
    {
        $this->actor->fillField('#user-username', $username);
        $this->actor->fillField('#user-email', $email);
        $this->actor->fillField('#user-password', $password);
        $this->actor->click('Save');
    }
}
