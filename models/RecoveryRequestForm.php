<?php

namespace dektrium\user\models;

use dektrium\user\helpers\ModuleTrait;
use yii\base\Model;

/**
 * Model for collecting data on password recovery init.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryRequestForm extends Model
{
    use ModuleTrait;

    /**
     * @var string
     */
    public $email;

    /**
     * @var \dektrium\user\models\User
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => \Yii::t('user', 'Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => $this->module->manager->userClass,
                'message' => \Yii::t('user', 'There is no user with such email.')
            ],
            ['email', function ($attribute) {
                $this->_user = $this->module->manager->findUserByEmail($this->email);
                if ($this->_user !== null && $this->getModule()->confirmable && !$this->_user->getIsConfirmed()) {
                    $this->addError($attribute, \Yii::t('user', 'You need to confirm your email address'));
                }
            }],
        ];
    }

    /**
     * Sends recovery message.
     *
     * @return bool
     */
    public function sendRecoveryMessage()
    {
        if ($this->validate()) {
            $this->_user->sendRecoveryMessage();

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'recovery-request-form';
    }
}
