<?php

namespace tests\_pages;

/**
 * Represents login page.
 *
 * @property \FunctionalTester $actor
 */
class LoginPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/security/login';

    /**
     * @param $login
     * @param $password
     */
    public function login($login, $password)
    {
        $this->actor->fillField('#login-form-login', $login);
        $this->actor->fillField('#login-form-password', $password);
        $this->actor->click('Sign in');
    }
}
