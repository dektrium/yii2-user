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

use dektrium\user\service\RegistrationService;
use yii\base\Model;

/**
 * Registration form collects user input on registration process and validates it.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationForm extends Model
{
    /**
     * @var string User email address
     */
    public $email;

    /**
     * @var string Username
     */
    public $username;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var RegistrationService
     */
    private $_service;

    /**
     * @return RegistrationService
     */
    public function getRegistrationService()
    {
        return $this->_service;
    }

    /**
     * @param RegistrationService $service
     */
    public function setRegistrationService(RegistrationService $service)
    {
        $this->_service = $service;
    }

    /**
     * RegistrationForm constructor.
     * @param RegistrationService $service
     * @param array $config
     */
    public function __construct(RegistrationService $service, array $config = [])
    {
        $this->setRegistrationService($service);

        parent::__construct($config);
    }

    /**
     * Returns mappings between registration form and User model. Keys of the array are properties of registration form
     * and values are properties of User model. For example:
     *
     * ```php
     * return [
     *     'email' => 'email',
     *     'username' => 'username',
     *     'password' => 'password',
     *     'first_name' => 'profile.first_name',
     * ];
     * ```
     *
     * Notice, you may use dot notation to set fields from registration model to the related model's properties. In the
     * above example we map first_name property of registration model to the first_name property of profile relation.
     *
     * @return array
     */
    public function getMappings()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $user = get_class(\Yii::createObject(User::className()));
        $rules = [
            // username rules
            'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameTrim'     => ['username', 'filter', 'filter' => 'trim'],
            'usernamePattern'  => ['username', 'match', 'pattern' => $user::$usernameRegexp],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique'   => [
                'username',
                'unique',
                'targetClass' => $user,
                'message' => \Yii::t('user', 'This username has already been taken')
            ],
            // email rules
            'emailTrim'     => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern'  => ['email', 'email'],
            'emailUnique'   => [
                'email',
                'unique',
                'targetClass' => $user,
                'message' => \Yii::t('user', 'This email address has already been taken')
            ],
        ];
        if (!$this->getRegistrationService()->isPasswordGeneratorEnabled) {
            $rules = array_merge($rules, [
                // password rules
                'passwordRequired' => ['password', 'required'],
                'passwordLength'   => ['password', 'string', 'min' => 6, 'max' => 72],
            ]);
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('user', 'Email'),
            'username' => \Yii::t('user', 'Username'),
            'password' => \Yii::t('user', 'Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }
}
