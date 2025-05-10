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

namespace AlexeiKaDev\Yii2User\models\query;

use AlexeiKaDev\Yii2User\models\Account;
use yii\authclient\ClientInterface as BaseClientInterface; // Yii's own interface
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
     * @param  string       $code
     * @return static The query object itself
     */
    public function byCode(string $code): static
    {
        return $this->andWhere(['code' => md5($code)]); // Consider security implications of MD5
    }

    /**
     * Finds an account by id.
     * @param  int      $id
     * @return static The query object itself
     */
    public function byId(int $id): static
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * Finds accounts by user_id.
     * @param  int      $userId
     * @return static The query object itself
     */
    public function byUser(int $userId): static
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * Finds an account by client.
     * @param  BaseClientInterface $client
     * @return static The query object itself
     */
    public function byClient(BaseClientInterface $client): static
    {
        return $this->andWhere([
            'provider' => $client->getId(),
            'client_id' => $client->getUserAttributes()['id'] ?? null, // Check if ID exists
        ]);
    }
}
