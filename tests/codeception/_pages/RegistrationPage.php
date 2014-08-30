<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents registration page.
 *
 * @property \FunctionalTester $actor
 */
class RegistrationPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/registration/register';

    /**
     * @param $username
     * @param $email
     * @param $password
     */
    public function register($username, $email, $password)
    {
        $this->actor->fillField('#register-form-username', $username);
        $this->actor->fillField('#register-form-email', $email);
        $this->actor->fillField('#register-form-password', $password);
        $this->actor->click('Sign up');
    }
}
