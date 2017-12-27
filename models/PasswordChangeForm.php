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

use dektrium\user\helpers\Password;
use dektrium\user\traits\ModuleTrait;
use Yii;
use yii\base\Model;
use yii\web\BadRequestHttpException;

/**
 * PasswordChangeForm prompts for the password of the currently logged in user. If it´s correct,
 * the user can set a new password for his account.
 *
 * @property User $user
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 */
class PasswordChangeForm extends Model
{
    use ModuleTrait;

    /** @var string */
    public $username;

    /** @var string */
    public $new_password;

    /** @var string */
    public $new_password_confirmation;

    /** @var string */
    public $current_password;

    /** @var User */
    private $_user;

    /** @return User */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = Yii::$app->user->identity;
        }

        return $this->_user;
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'newPasswordRequired' => ['new_password', 'required'],
            'newPasswordConfirmationRequired' => ['new_password_confirmation', 'required'],
            'newPasswordLength' => ['new_password', 'string', 'max' => 72, 'min' => 6],
            'newPasswordConfirmation' => ['new_password_confirmation', 'compare', 'compareAttribute' => 'new_password'],
            'currentPasswordRequired' => ['current_password', 'required'],
            'currentPasswordValidate' => ['current_password', function ($attr) {
                if (!Password::validate($this->$attr, $this->user->password_hash)) {
                    $this->addError($attr, Yii::t('user', 'Current password is not valid'));
                }
            }],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'new_password' => Yii::t('user', 'New password'),
            'new_password_confirmation' => Yii::t('user', 'New password confirmation'),
            'current_password' => Yii::t('user', 'Current password'),
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'password-change-form';
    }

    /** @inheritdoc */
    public function save()
    {
        $this->user->password = $this->new_password;
        return $this->user->save(true, ['password', 'password_hash']);
    }

}
