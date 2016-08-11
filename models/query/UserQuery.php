<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\models\query;

use dektrium\user\models\User;
use yii\db\ActiveQuery;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return UserQuery
     */
    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param  string $email
     * @return UserQuery
     */
    public function byEmail($email)
    {
        return $this->andWhere(['email' => $email]);
    }

    /**
     * @param  string $username
     * @return UserQuery
     */
    public function byUsername($username)
    {
        return $this->andWhere(['username' => $username]);
    }

    /**
     * @param  string $emailOrUsername
     * @return UserQuery
     */
    public function byEmailOrUsername($emailOrUsername)
    {
        if (filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL)) {
            return $this->byEmail($emailOrUsername);
        }

        return $this->byUsername($emailOrUsername);
    }

    /**
     * @param  bool $confirmed
     * @return $this
     */
    public function confirmed($confirmed = true)
    {
        return $confirmed
            ? $this->andWhere('confirmed_at IS NOT NULL')
            : $this->andWhere('confirmed_at IS NULL');
    }

    /**
     * @param  bool $blocked
     * @return $this
     */
    public function blocked($blocked = true)
    {
        return $blocked
            ? $this->andWhere('blocked_at IS NOT NULL')
            : $this->andWhere('blocked_at IS NULL');
    }

    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}