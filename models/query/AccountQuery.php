<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\models\query;

use AlexeiKaDev\Yii2User\models\Account;
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\db\ActiveQuery;

/**
 * @method Account|null one($db = null)
 * @method Account[]    all($db = null)
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AccountQuery extends ActiveQuery
{
    /**
     * Finds an account by code.
     * Uses SHA-256 for secure hashing instead of deprecated MD5.
     * @param  string $code
     * @return self The query object itself
     */
    public function byCode($code)
    {
        return $this->andWhere(['code' => hash('sha256', $code)]);
    }

    /**
     * Finds an account by id.
     * @param  int $id
     * @return self The query object itself
     */
    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * Finds accounts by user_id.
     * @param  int $userId
     * @return self The query object itself
     */
    public function byUser($userId)
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * Finds an account by client.
     * @param  BaseClientInterface $client
     * @return self The query object itself
     */
    public function byClient($client)
    {
        $attributes = $client->getUserAttributes();
        return $this->andWhere([
            'provider' => $client->getId(),
            'client_id' => isset($attributes['id']) ? $attributes['id'] : null,
        ]);
    }
}
