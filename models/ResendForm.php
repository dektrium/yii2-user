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
 * ResendForm gets user email address and if user with given email is registered it sends new confirmation message
 * to him in case he did not validate his email.
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
        ];
    }

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
    public function formName()
    {
        return 'resend-form';
    }

    /**
     * @return array|User|null
     */
    public function getUser()
    {
        /** @var User $user */
        $user = \Yii::createObject(User::className());
        return $user::find()->byEmail($this->email)->one();
    }
}
