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
	 * @var \dektrium\user\models\User
	 */
	private $_user;

	/**
	 * Creates a form model given a token.
	 *
	 * @param integer $id
	 * @param string  $token
	 * @param array   $config name-value pairs that will be used to initialize the object properties
	 *
	 * @throws \yii\base\InvalidParamException if token is empty or not valid
	 */
	public function __construct($id, $token, $config = [])
	{
		$query = $this->module->factory->createUserQuery();
		$this->_user = $query->where(['id' => $id, 'recovery_token' => $token])->one();
		if (!$this->_user) {
			throw new InvalidParamException('Wrong password reset token');
		}
		if ($this->_user->isRecoveryPeriodExpired) {
			throw new InvalidParamException('Token has been expired');
		}
		parent::__construct($config);
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