<?php

namespace dektrium\user\tests\_pages;

use yii\codeception\BasePage;

class PasswordSettingsPage extends BasePage
{
    /**
     * @var string
     */
    public $route = '/user/settings/password';

    /**
     * @param $currentPassword
     * @param $password
     */
    public function updatePassword($currentPassword, $password)
    {
        $this->guy->fillField('#user-current_password', $currentPassword);
        $this->guy->fillField('#user-password', $password);
        $this->guy->click('Update password');
    }
}
