<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\events;

use dektrium\user\models\Account;
use yii\authclient\ClientInterface;
use yii\base\Event;

/**
 * @property Account         $account
 * @property ClientInterface $client
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AuthEvent extends Event
{
    /**
     * @var ClientInterface
     */
    private $_client;

    /**
     * @var Account
     */
    private $_account;

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->_account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account)
    {
        $this->_account = $account;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->_client = $client;
    }
}