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
use yii\base\Model;

/**
 * ResendForm gets user email address and validates if user has already confirmed his account. If so, it shows error
 * message, otherwise it generates and sends new confirmation token to user.
 *
 * @property User $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ResendForm extends Model
{
    use ModuleTrait;

    /**
     * @var string
     */
    public $email;

    /**
     * @var User
     */
    private $_user;

    /**
     * @return User
     */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = $this->module->manager->findUserByEmail($this->email);
        }

        return $this->_user;
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => $this->getModule()->manager->userClass],
            ['email', function () {
                if ($this->user != null && $this->user->isConfirmed) {
                    $this->addError('email', \Yii::t('user', 'This account has already been confirmed'));
                }
            }],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('user', 'Email'),
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'resend-form';
    }

    /**
     * Creates new confirmation token and sends it to the user.
     *
     * @return bool
     */
    public function resend()
    {
        if ($this->validate()) {
            $token = $this->module->manager->createToken([
                'user_id' => $this->user->id,
                'type'    => Token::TYPE_CONFIRMATION
            ]);
            $token->save(false);
            $this->module->mailer->sendConfirmationMessage($this->user, $token);
            \Yii::$app->session->setFlash('user.confirmation_sent');
            return true;
        }

        return false;
    }
}
