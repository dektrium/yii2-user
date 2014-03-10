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

use yii\base\InvalidParamException;
use yii\base\Model;

/**
 * Model for collecting data on password recovery.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class PasswordRecovery extends Model
{
	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $token;

	/**
	 * @var \dektrium\user\models\User
	 */
	private $_user;

	/**
	 * @inheritdoc
	 * @throws \yii\base\InvalidParamException
	 */
	public function init()
	{
		parent::init();
		if ($this->id == null || $this->token == null) {
			throw new \RuntimeException('Id and token should be passed to config');
		}
		
		$query = $this->module->factory->createUserQuery();
		$this->_user = $query->where(['id' => $this->id, 'recovery_token' => $this->token])->one();
		if (!$this->_user) {
			throw new InvalidParamException('Wrong password reset token');
		}
		if ($this->_user->isRecoveryPeriodExpired) {
			throw new InvalidParamException('Token has been expired');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'password' => \Yii::t('user', 'Password'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		return [
			'default' => ['password']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['password', 'required'],
			['password', 'string', 'min' => 6],
		];
	}

	/**
	 * @return null|\dektrium\user\Module
	 */
	protected function getModule()
	{
		return \Yii::$app->getModule('user');
	}
}