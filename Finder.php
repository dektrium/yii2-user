<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User;

use AlexeiKaDev\Yii2User\models\Account;
use AlexeiKaDev\Yii2User\models\Profile;
use AlexeiKaDev\Yii2User\models\query\AccountQuery;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\User;
use yii\base\BaseObject;
use yii\db\ActiveQuery;

/**
 * Finder provides some useful methods for finding active record models.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Finder extends BaseObject
{
    /** @var ActiveQuery */
    protected $userQuery;

    /** @var ActiveQuery */
    protected $tokenQuery;

    /** @var AccountQuery */
    protected $accountQuery;

    /** @var ActiveQuery */
    protected $profileQuery;

    /**
     * @return ActiveQuery
     */
    public function getUserQuery()
    {
        return $this->userQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getTokenQuery()
    {
        return $this->tokenQuery;
    }

    /**
     * @return AccountQuery
     */
    public function getAccountQuery()
    {
        return $this->accountQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getProfileQuery()
    {
        return $this->profileQuery;
    }

    /** @param AccountQuery $accountQuery */
    public function setAccountQuery($accountQuery)
    {
        $this->accountQuery = $accountQuery;
    }

    /** @param ActiveQuery $userQuery */
    public function setUserQuery($userQuery)
    {
        $this->userQuery = $userQuery;
    }

    /** @param ActiveQuery $tokenQuery */
    public function setTokenQuery($tokenQuery)
    {
        $this->tokenQuery = $tokenQuery;
    }

    /** @param ActiveQuery $profileQuery */
    public function setProfileQuery($profileQuery)
    {
        $this->profileQuery = $profileQuery;
    }

    /**
     * Finds a user by the given id.
     *
     * @param int $id User id to be used on search.
     *
     * @return User|null
     */
    public function findUserById($id)
    {
        return $this->findUser(['id' => $id])->one();
    }

    /**
     * Finds a user by the given username.
     *
     * @param string $username Username to be used on search.
     *
     * @return User|null
     */
    public function findUserByUsername($username)
    {
        return $this->findUser(['username' => $username])->one();
    }

    /**
     * Finds a user by the given email.
     *
     * @param string $email Email to be used on search.
     *
     * @return User|null
     */
    public function findUserByEmail($email)
    {
        return $this->findUser(['email' => $email])->one();
    }

    /**
     * Finds a user by the given username or email.
     *
     * @param string $usernameOrEmail Username or email to be used on search.
     *
     * @return User|null
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
     * @param mixed $condition Condition to be used on search.
     *
     * @return ActiveQuery
     */
    public function findUser($condition)
    {
        return $this->userQuery->where($condition);
    }

    /**
     * @return AccountQuery
     */
    public function findAccount()
    {
        return $this->accountQuery;
    }

    /**
     * Finds an account by id.
     *
     * @param int $id
     *
     * @return Account|null
     */
    public function findAccountById($id)
    {
        return $this->accountQuery->where(['id' => $id])->one();
    }

    /**
     * Finds a token by user id and code.
     *
     * @param mixed $condition
     *
     * @return ActiveQuery
     */
    public function findToken($condition)
    {
        return $this->tokenQuery->where($condition);
    }

    /**
     * Finds a token by params.
     *
     * @param integer $userId
     * @param string  $code
     * @param integer $type
     *
     * @return Token|null
     */
    public function findTokenByParams($userId, $code, $type)
    {
        return $this->findToken([
            'user_id' => $userId,
            'code' => $code,
            'type' => $type,
        ])->one();
    }

    /**
     * Finds a profile by user id.
     *
     * @param int $id
     *
     * @return Profile|null
     */
    public function findProfileById($id)
    {
        return $this->findProfile(['user_id' => $id])->one();
    }

    /**
     * Finds a profile.
     *
     * @param mixed $condition
     *
     * @return ActiveQuery
     */
    public function findProfile($condition)
    {
        return $this->profileQuery->where($condition);
    }
}
