<?php

namespace tests\_pages;

use yii\codeception\BasePage;

class EmailSettingsPage extends BasePage
{
	/**
	 * @var string
	 */
	public $route = '/user/settings/email';

	/**
	 * @param $currentPassword
	 * @param $email
	 */
	public function update($currentPassword, $email)
	{
		$this->guy->fillField('#user-current_password', $currentPassword);
		$this->guy->fillField('#user-unconfirmed_email', $email);
		$this->guy->click('Update email');
	}
}