<?php namespace tests\_pages;

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
		$this->guy->fillField('#registration-form-username', $username);
		$this->guy->fillField('#registration-form-email', $email);
		$this->guy->fillField('#registration-form-password', $password);
		$this->guy->click('Register');
	}
}