<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents password settings page.
 *
 * @property \FunctionalTester $actor
 */
class PasswordSettingsPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/settings/password';

    /**
     * @param $currentPassword
     * @param $password
     */
    public function updatePassword($currentPassword, $password)
    {
        $this->actor->fillField('#user-current_password', $currentPassword);
        $this->actor->fillField('#user-password', $password);
        $this->actor->click('Save');
    }
}
