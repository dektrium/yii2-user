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

use yii\base\Model;

/**
 * Model that manages resending confirmation tokens to users.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Resend extends Model
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
            'email' => \Yii::t('user', 'Email'),
            'verifyCode' => \Yii::t('user', 'Verification Code'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => $this->getModule()->factory->userClass],
            ['email', 'validateEmail'],
        ];

        if (in_array('resend', $this->getModule()->captcha)) {
            $rules[] = ['verifyCode', 'captcha', 'captchaAction' => 'user/default/captcha'];
        }

        return $rules;
    }

    /**
     * Validates if user has already been confirmed or not.
     */
    public function validateEmail()
    {
        if ($this->getUser() != null && $this->getUser()->getIsConfirmed()) {
            $this->addError('email', \Yii::t('user', 'This account has already been confirmed'));
        }
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'resend-form';
    }

    /**
     * @return \dektrium\user\models\UserInterface
     */
    public function getUser()
    {
        if ($this->_user == null) {
            $query = $this->getModule()->factory->createUserQuery();
            $this->_user = $query->where(['email' => $this->email])->one();
        }

        return $this->_user;
    }

    /**
     * @return null|\dektrium\user\Module
     */
    protected function getModule()
    {
        return \Yii::$app->getModule('user');
    }
}
