<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\traits;

use dektrium\user\events\AuthEvent;
use dektrium\user\events\ConnectEvent;
use dektrium\user\events\FormEvent;
use dektrium\user\events\ProfileEvent;
use dektrium\user\events\ResetPasswordEvent;
use dektrium\user\events\UserEvent;
use dektrium\user\models\Account;
use dektrium\user\models\Profile;
use dektrium\user\models\RecoveryForm;
use dektrium\user\models\Token;
use dektrium\user\models\User;
use yii\authclient\ClientInterface;
use yii\base\Model;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait EventTrait
{
    /**
     * @param  Model     $form
     * @return FormEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFormEvent(Model $form)
    {
        return \Yii::createObject(['class' => FormEvent::className(), 'form' => $form]);
    }

    /**
     * @param  User      $user
     * @return UserEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getUserEvent(User $user)
    {
        return \Yii::createObject(['class' => UserEvent::className(), 'user' => $user]);
    }

    /**
     * @param  Profile      $profile
     * @return ProfileEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getProfileEvent(Profile $profile)
    {
        return \Yii::createObject(['class' => ProfileEvent::className(), 'profile' => $profile]);
    }


    /**
     * @param  Account      $account
     * @param  User         $user
     * @return ConnectEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getConnectEvent(Account $account, User $user)
    {
        return \Yii::createObject(['class' => ConnectEvent::className(), 'account' => $account, 'user' => $user]);
    }

    /**
     * @param  Account         $account
     * @param  ClientInterface $client
     * @return AuthEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getAuthEvent(Account $account, ClientInterface $client)
    {
        return \Yii::createObject(['class' => AuthEvent::className(), 'account' => $account, 'client' => $client]);
    }

    /**
     * @param  Token        $token
     * @param  RecoveryForm $form
     * @return ResetPasswordEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getResetPasswordEvent(Token $token, RecoveryForm $form = null)
    {
        return \Yii::createObject(['class' => ResetPasswordEvent::className(), 'token' => $token, 'form' => $form]);
    }
}