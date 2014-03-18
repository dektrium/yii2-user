<?php

namespace dektrium\user\tests\_pages;

use yii\codeception\BasePage;

class RecoveryPage extends BasePage
{
    /**
     * @var string
     */
    public $route = '/user/recovery/request';

    /**
     * @param $email
     */
    public function resend($email)
    {
        $this->guy->fillField('#recovery-request-form-email', $email);
        $this->guy->click('Send');
    }
}
