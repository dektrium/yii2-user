<?php

namespace tests\_pages;

/**
 * Represents resend page.
 *
 * @property \FunctionalTester $actor
 */
class ResendPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/registration/resend';

    /**
     * @param $email
     */
    public function resend($email)
    {
        $this->actor->fillField('#resend-form-email', $email);
        $this->actor->click('Continue');
    }
}
