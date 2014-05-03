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

use yii\base\Model;

/**
 * Model that manages resending confirmation tokens to users.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ResendForm extends Model
{
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
            'email' => \Yii::t('user', 'Email'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => $this->getModule()->manager->userClass],
            ['email', 'validateEmail'],
        ];
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
     * @return \dektrium\user\models\User
     */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = $this->module->manager->findUserByEmail($this->email);
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
