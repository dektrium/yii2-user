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
        $this->guy->fillField('#user-username', $username);
        $this->guy->fillField('#user-email', $email);
        $this->guy->fillField('#user-password', $password);
        $this->guy->click('Register');
    }
}
