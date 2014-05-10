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
use dektrium\user\helpers\Password;
use yii\base\Model;

/**
 * Registration form collects user input on registration process, validates it and creates new User model.
 * If needed, it will create confirmation token and send it to user.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationForm extends Model
{
    use ModuleTrait;

    /** @var string */
    public $email;

    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z]\w+$/'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => $this->module->manager->userClass,
                'message' => \Yii::t('user', 'This username has already been taken')],
            ['username', 'string', 'min' => 3, 'max' => 20],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => $this->module->manager->userClass,
                'message' => \Yii::t('user', 'This email address has already been taken')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email'    => \Yii::t('user', 'Email'),
            'username' => \Yii::t('user', 'Username'),
            'password' => \Yii::t('user', 'Password'),
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'register-form';
    }

    /**
     * Registers a new user account.
     * If confirmable is enabled, it will generate new confirmation token and send it to user.
     *
     * @return bool
     */
    public function register()
    {
        if ($this->validate()) {
            $user = $this->module->manager->createUser([
                'scenario' => 'register',
                'email'    => $this->email,
                'username' => $this->username,
                'password' => $this->password
            ]);

            if ($user->save()) {
                if ($this->module->confirmable) {
                    $token = $this->module->manager->createToken([
                        'user_id' => $user->id,
                        'type'    => Token::TYPE_CONFIRMATION
                    ]);
                    $token->save(false);
                    $this->module->mailer->sendConfirmationMessage($user, $token);
                    \Yii::$app->session->setFlash('user.confirmation_sent');
                } else {
                    \Yii::$app->session->setFlash('user.registration_finished');
                    \Yii::$app->user->login($user);
                }
                return true;
            }
        }

        return false;
    }
}