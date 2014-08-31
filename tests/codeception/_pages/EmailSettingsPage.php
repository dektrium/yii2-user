<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents email settings page.
 *
 * @property \FunctionalTester $actor
 */
class EmailSettingsPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/settings/email';

    /**
     * @param $currentPassword
     * @param $email
     */
    public function updateEmail($currentPassword, $email)
    {
        $this->actor->fillField('#user-current_password', $currentPassword);
        $this->actor->fillField('#user-unconfirmed_email', $email);
        $this->actor->click('Save');
    }
}
