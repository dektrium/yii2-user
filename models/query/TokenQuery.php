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
use dektrium\user\models\Token;
use yii\db\ActiveQuery;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class TokenQuery extends ActiveQuery
{
    /**
     * @param  int $userId
     * @return TokenQuery
     */
    public function byUserId($userId)
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * @param  string $type
     * @return TokenQuery
     */
    public function byType($type)
    {
        return $this->andWhere(['type' => $type]);
    }

    /**
     * @param  string $code
     * @return TokenQuery
     */
    public function byCode($code)
    {
        return $this->andWhere(['code' => $code]);
    }

    /**
     * @inheritdoc
     * @return Token[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Token|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}