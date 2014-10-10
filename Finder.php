<?php

namespace dektrium\user;

use yii\base\Object;
use yii\db\ActiveQuery;

/**
 * Finder provides some useful methods for finding active record models.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Finder extends Object
{
    /** @var ActiveQuery */
    protected $userQuery;

    /** @var ActiveQuery */
    protected $accountQuery;

    /** @param ActiveQuery $accountQuery */
    public function setAccountQuery(ActiveQuery $accountQuery)
    {
        $this->accountQuery = $accountQuery;
    }

    /** @param ActiveQuery $userQuery */
    public function setUserQuery(ActiveQuery $userQuery)
    {
        $this->userQuery = $userQuery;
    }

    /**
     * Finds a user by the given id.
     *
     * @param  integer     $id User id to be used on search.
     * @return models\User
     */
    public function findUserById($id)
    {
        return $this->findUser(['id' => $id])->one();
    }

    /**
     * Finds a user by the given username.
     *
     * @param  string      $username Username to be used on search.
     * @return models\User
     */
    public function findUserByUsername($username)
    {
        return $this->findUser(['username' => $username])->one();
    }

    /**
     * Finds a user by the given email.
     *
     * @param  string      $email Email to be used on search.
     * @return models\User
     */
    public function findUserByEmail($email)
    {
        return $this->findUser(['email' => $email])->one();
    }

    /**
     * Finds a user by the given username or email.
     *
     * @param  string      $usernameOrEmail Username or email to be used on search.
     * @return models\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * Finds a user by the given condition.
     *
     * @param  mixed               $condition Condition to be used on search.
     * @return \yii\db\ActiveQuery
     */
    public function findUser($condition)
    {
        return $this->userQuery->where($condition);
    }

    /**
     * Finds an account by id.
     *
     * @param integer $id
     * @return models\Account|null
     */
    public function findAccountById($id)
    {
        return $this->accountQuery->where(['id' => $id])->one();
    }

    /**
     * Finds an account by client id and provider name.
     *
     * @param string $provider
     * @param string $clientId
     * @return models\Account|null
     */
    public function findAccountByProviderAndClientId($provider, $clientId)
    {
        return $this->accountQuery->where([
            'provider'  => $provider,
            'client_id' => $clientId
        ])->one();
    }
}
