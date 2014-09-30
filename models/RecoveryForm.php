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
     * @var Token
     */
    public $token;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidParamException
     */
    public function init()
    {
        parent::init();
        if ($this->token == null) {
            throw new \RuntimeException('Token should be passed to config');
        }

        if ($this->token->getIsExpired() || $this->token->user === null) {
            throw new InvalidParamException('Invalid token');
        }
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'password' => \Yii::t('user', 'Password'),
        ];
    }

    /** @inheritdoc */
    public function scenarios()
    {
        return [
            'default' => ['password']
        ];
    }

    /** @inheritdoc */
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
            $this->token->user->resetPassword($this->password);
            $this->token->delete();
            \Yii::$app->session->setFlash('user.recovery_finished');
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
