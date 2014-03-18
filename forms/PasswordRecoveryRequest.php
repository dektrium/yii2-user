<?php

namespace dektrium\user\forms;

use yii\base\Model;

/**
 * Model for collecting data on password recovery init.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class PasswordRecoveryRequest extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $verifyCode;

    /**
     * @var \dektrium\user\models\UserInterface
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => \Yii::t('user', 'Email'),
            'verifyCode' => \Yii::t('user', 'Captcha'),
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
                'targetClass' => $this->module->factory->userClass,
                'message' => \Yii::t('user', 'There is no user with such email.')
            ],
            ['email', function ($attribute) {
                $query = $this->getModule()->factory->createUserQuery();
                $this->_user = $query->where(['email' => $this->email])->one();
                if ($this->_user !== null && $this->getModule()->confirmable && !$this->_user->getIsConfirmed()) {
                    $this->addError($attribute, \Yii::t('user', 'You must confirm your account first'));
                }
            }],
            ['verifyCode', 'captcha',
                'captchaAction' => 'user/default/captcha',
                'skipOnEmpty' => !in_array('recovery', $this->module->captcha)
            ]
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

    /**
     * @return null|\dektrium\user\Module
     */
    protected function getModule()
    {
        return \Yii::$app->getModule('user');
    }
}
