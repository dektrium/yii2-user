<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\events;

use AlexeiKaDev\Yii2User\models\Account;
use AlexeiKaDev\Yii2User\models\User;
use yii\base\Event;

/**
 * @property User    $model
 * @property Account $account
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ConnectEvent extends Event
{
    /**
     * @var User
     */
    private User $_user;

    /**
     * @var Account
     */
    private Account $_account;

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->_account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account): void
    {
        $this->_account = $account;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->_user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->_user = $user;
    }
}
