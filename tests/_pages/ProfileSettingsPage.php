<?php

namespace dektrium\user\tests\_pages;

use yii\codeception\BasePage;

class ProfileSettingsPage extends BasePage
{
    /**
     * @var string
     */
    public $route = '/user/settings/profile';

    /**
     * @param $name
     * @param $email
     * @param $website
     * @param $location
     * @param $gravatar_email
     * @param $bio
     */
    public function update($name = null, $email = null, $website = null, $location = null, $gravatar_email = null, $bio = null)
    {
        $this->guy->fillField('#profile-name', $name);
        $this->guy->fillField('#profile-public_email', $email);
        $this->guy->fillField('#profile-website', $website);
        $this->guy->fillField('#profile-location', $location);
        $this->guy->fillField('#profile-gravatar_email', $gravatar_email);
        $this->guy->fillField('#profile-bio', $bio);
        $this->guy->click('Update profile');
    }
}
