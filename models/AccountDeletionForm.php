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
 * AccountDeletionForm prompts for the password of the currently logged in user. If it´s correct,
 * the account gets deleted.
 *
 * @property User $user
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 */
class AccountDeletionForm extends Model
{
    use ModuleTrait;

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
            'current_password' => Yii::t('user', 'Current password'),
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'account-deletion-form';
    }

    /**
     * Do the dirty work.
     *
     * @return bool
     */
    protected function delete()
    {
        if (!$this->module->enableAccountDelete) {
            throw new NotFoundHttpException(\Yii::t('user', 'Account deletion is deactivated'));
        }

        if (!$this->validate()) {
            return false;
        }

        return false;
    }

}
