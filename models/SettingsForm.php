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
use dektrium\user\Module;
use yii\base\Model;
use yii\base\NotSupportedException;

/**
 * SettingsForm gets user's username, email and password and changes them.
 *
 * @property User $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsForm extends Model
{
    use ModuleTrait;

    /** @var string */
    public $email;

    /** @var string */
    public $username;

    /** @var string */
    public $new_password;

    /** @var string */
    public $current_password;

    /** @var User */
    private $_user;

    /** @return User */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = \Yii::$app->user->identity;
        }

        return $this->_user;
    }

    /** @inheritdoc */
    public function init()
    {
        $this->setAttributes([
            'username' => $this->user->username,
            'email'    => $this->user->unconfirmed_email ?: $this->user->email
        ]);
        parent::init();
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['username', 'email', 'current_password'], 'required'],
            [['username', 'email'], 'filter', 'filter' => 'trim'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z]\w+$/'],
            ['username', 'string', 'min' => 3, 'max' => 20],
            ['email', 'email'],
            [['email', 'username'], 'unique', 'when' => function ($model, $attribute) {
                return $this->user->$attribute != $model->$attribute;
            }, 'targetClass' => $this->module->manager->userClass],
            ['new_password', 'string', 'min' => 6],
            ['current_password', function ($attr) {
                if (!Password::validate($this->$attr, $this->user->password_hash)) {
                    $this->addError($attr, \Yii::t('user', 'Current password is not valid'));
                }
            }]
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email'            => \Yii::t('user', 'Email'),
            'username'         => \Yii::t('user', 'Username'),
            'new_password'     => \Yii::t('user', 'New password'),
            'current_password' => \Yii::t('user', 'Current password')
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'settings-form';
    }

    /**
     * Saves new account settings.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $this->user->scenario = 'settings';
            // change username
            $this->user->username = $this->username;
            // change password
            $this->user->password = $this->new_password;
            // change email
            if ($this->email == $this->user->email && $this->user->unconfirmed_email != null) {
                $this->user->unconfirmed_email = null;
                \Yii::$app->session->setFlash('info', \Yii::t('user', 'You have successfully cancelled email changing process'));
            } else if ($this->email != $this->user->email) {
                switch ($this->module->emailChangeStrategy) {
                    case Module::STRATEGY_INSECURE:
                        $this->insecureEmailChange(); break;
                    case Module::STRATEGY_DEFAULT:
                        $this->defaultEmailChange(); break;
                    case Module::STRATEGY_SECURE:
                        $this->secureEmailChange(); break;
                    default:
                        throw new \OutOfBoundsException('Invalid email changing strategy');
                }
            }
            return $this->user->save();
        }

        return false;
    }

    /**
     * Changes user's email address to given without any confirmation.
     */
    protected function insecureEmailChange()
    {
        $this->user->email = $this->email;
        \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your email address has been successfully changed'));
    }

    /**
     * Sends a confirmation message to user's email address with link to confirm changing of email.
     */
    protected function defaultEmailChange()
    {
        $this->user->unconfirmed_email = $this->email;
        $token = $this->module->manager->createToken([
            'user_id' => $this->user->id,
            'type'    => Token::TYPE_CONFIRM_NEW_EMAIL
        ]);
        $token->save(false);
        $this->module->mailer->sendReconfirmationMessage($this->user, $token);
        \Yii::$app->session->setFlash('info', \Yii::t('user', 'Confirmation message has been sent to your new email address'));
    }

    protected function secureEmailChange()
    {
        throw new NotSupportedException('Secure email changing strategy is not yet implemented');
    }
}