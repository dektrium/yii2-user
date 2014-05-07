<?php

namespace dektrium\user\tests\_pages;

use yii\codeception\BasePage;

class RegisterPage extends BasePage
{
    /**
     * @var string
     */
    public $route = '/user/registration/register';

    /**
     * @param $username
     * @param $email
     * @param $password
     */
    public function register($username, $email, $password)
    {
        $this->guy->fillField('#register-form-username', $username);
        $this->guy->fillField('#register-form-email', $email);
        $this->guy->fillField('#register-form-password', $password);
        $this->guy->click('Sign up');
    }
}
