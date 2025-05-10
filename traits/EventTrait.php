<?php

declare(strict_types=1);

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\traits;

use AlexeiKaDev\Yii2User\events\AuthEvent;
use AlexeiKaDev\Yii2User\events\ConnectEvent;
use AlexeiKaDev\Yii2User\events\FormEvent;
use AlexeiKaDev\Yii2User\events\ProfileEvent;
use AlexeiKaDev\Yii2User\events\ResetPasswordEvent;
use AlexeiKaDev\Yii2User\events\UserEvent;
use AlexeiKaDev\Yii2User\models\Account;
use AlexeiKaDev\Yii2User\models\Profile;
use AlexeiKaDev\Yii2User\models\RecoveryForm;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\User;
use Yii;
use yii\authclient\ClientInterface as BaseClientInterface;
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
    protected function getFormEvent(Model $form): FormEvent
    {
        return Yii::createObject(['class' => FormEvent::class, 'form' => $form]);
    }

    /**
     * @param  User      $user
     * @return UserEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getUserEvent(User $user): UserEvent
    {
        return Yii::createObject(['class' => UserEvent::class, 'user' => $user]);
    }

    /**
     * @param  Profile      $profile
     * @return ProfileEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getProfileEvent(Profile $profile): ProfileEvent
    {
        return Yii::createObject(['class' => ProfileEvent::class, 'profile' => $profile]);
    }

    /**
     * @param  Account      $account
     * @param  User         $user
     * @return ConnectEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getConnectEvent(Account $account, User $user): ConnectEvent
    {
        return Yii::createObject(['class' => ConnectEvent::class, 'account' => $account, 'user' => $user]);
    }

    /**
     * @param  Account           $account
     * @param  BaseClientInterface $client
     * @return AuthEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getAuthEvent(Account $account, BaseClientInterface $client): AuthEvent
    {
        return Yii::createObject(['class' => AuthEvent::class, 'account' => $account, 'client' => $client]);
    }

    /**
     * @param  Token|null        $token
     * @param  RecoveryForm|null $form
     * @return ResetPasswordEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getResetPasswordEvent(?Token $token = null, ?RecoveryForm $form = null): ResetPasswordEvent
    {
        return Yii::createObject(['class' => ResetPasswordEvent::class, 'token' => $token, 'form' => $form]);
    }
}
