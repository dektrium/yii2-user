<?php

namespace tests\_pages;

use yii\codeception\BasePage;

/**
 * Represents resend page.
 *
 * @property \FunctionalTester $actor
 */
class RecoveryPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/recovery/request';

    /**
     * @param $email
     */
    public function recover($email)
    {
        $this->actor->fillField('#recovery-form-email', $email);
        $this->actor->click('Continue');
    }
}
