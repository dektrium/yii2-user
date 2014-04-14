<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\models;

use dektrium\user\helpers\ModuleTrait;
use yii\base\InvalidParamException;
use yii\base\Model;

/**
 * Model for collecting data on password recovery.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryForm extends Model
{
    use ModuleTrait;

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

        $this->_user = $this->module->manager->findUserByIdAndRecoveryToken($this->id, $this->token);
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
     * Resets user's password.
     *
     * @return bool
     */
    public function resetPassword()
    {
        if ($this->validate()) {
            $this->_user->resetPassword($this->password);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'recovery-form';
    }
}
