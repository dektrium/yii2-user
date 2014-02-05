<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user\forms;

use dektrium\user\models\UserInterface;
use yii\base\Model;

/**
 * Recovery form manages requesting recovery token and resetting password.
 *
 * @property UserInterface $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Recovery extends Model
{
	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var string
	 */
	public $verifyCode;

	/**
	 * @var UserInterface
	 */
	private $_user;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'email' => \Yii::t('user', 'Email'),
			'password' => \Yii::t('user', 'Password'),
			'verifyCode' => \Yii::t('user', 'Verification Code'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		$attributes = ['email'];
		if (in_array('recovery', $this->getModule()->captcha)) {
			$attributes[] = 'verifyCode';
		}

		return [
			'request' => $attributes,
			'reset' => ['password']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		$rules = [
			['email', 'required', 'on' => 'request'],
			['email', 'email', 'on' => 'request'],
			['email', 'exist', 'targetClass' => $this->getModule()->factory->modelClass, 'on' => 'request'],
			['email', 'validateUserConfirmed', 'on' => 'request'],
			['password', 'required', 'on' => 'reset'],
			['password', 'string', 'min' => 6, 'on' => 'reset'],
		];

		if (in_array('recovery', $this->getModule()->captcha)) {
			$rules[] = ['verifyCode', 'captcha', 'on' => 'request', 'captchaAction' => 'user/default/captcha'];
		}

		return $rules;
	}

	/**
	 * Validates that user has confirmed email.
	 */
	public function validateUserConfirmed()
	{
		$query = $this->getModule()->factory->createQuery();
		$this->user = $query->where(['email' => $this->email])->one();
		if ($this->user !== null && $this->getModule()->confirmable && !$this->user->isConfirmed) {
			$this->addError('email', 'You must confirm your account first');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function formName()
	{
		return 'recovery-form';
	}

	/**
	 * Resets user's password.
	 *
	 * @return bool
	 */
	public function reset()
	{
		if ($this->validate() && $this->user->reset($this->password)) {
			\Yii::$app->getSession()->setFlash('password_reset');
			return true;
		}

		return false;
	}

	/**
	 * @return UserInterface
	 */
	public function getUser()
	{
		return $this->_user;
	}

	/**
	 * @param UserInterface $user
	 */
	public function setUser(UserInterface $user)
	{
		$this->_user = $user;
	}

	/**
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		return \Yii::$app->getModule('user');
	}
}