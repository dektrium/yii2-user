<?php

namespace dektrium\user\tests\_pages;

use yii\codeception\BasePage;

class LoginPage extends BasePage
{
    /**
     * @var string
     */
    public $route = '/user/auth/login';

    /**
     * @param $login
     * @param $password
     */
    public function login($login, $password)
    {
        $this->guy->fillField('#login-form-login', $login);
        $this->guy->fillField('#login-form-password', $password);
        $this->guy->click('Log in');
    }
}
