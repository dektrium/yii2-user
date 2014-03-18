<?php

/*
* This file is part of the Dektrium project.
*
* (c) Dektrium project <http://github.com/dektrium/>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace dektrium\user\models;

use yii\db\ActiveQuery;

/**
 * UserQuery
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class UserQuery extends ActiveQuery
{
    /**
     * Only confirmed users.
     *
     * @return $this
     */
    public function confirmed()
    {
        $this->andWhere('confirmed_at IS NOT NULL');

        return $this;
    }

    /**
     * Only unconfirmed users.
     *
     * @return $this
     */
    public function unconfirmed()
    {
        $this->andWhere('confirmed_at IS NULL');

        return $this;
    }
}
