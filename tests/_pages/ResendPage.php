<?php

namespace dektrium\user\tests\_pages;

use yii\codeception\BasePage;

class ResendPage extends BasePage
{
    /**
     * @var string
     */
    public $route = '/user/registration/resend';

    /**
     * @param $email
     */
    public function resend($email)
    {
        $this->guy->fillField('#resend-form-email', $email);
        $this->guy->click('Send');
    }
}
