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

/**
 * This is the ActiveQuery class for [[LoginAttempt]].
 *
 * @see LoginAttempt
 * @author jkmssoft
 */
class LoginAttemptQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return LoginAttempt[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return LoginAttempt|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
