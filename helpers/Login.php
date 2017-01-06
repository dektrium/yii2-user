<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use dektrium\user\models\User;

/**
 * Login helper.
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 */
class Login
{
    /**
     * Gets all users to generate the dropdown list when in debug mode.
     *
     * @return string
     */
    public static function loginList()
    {
        return ArrayHelper::map(User::find()->where(['blocked_at' => null])->all(), 'username', function ($user) {
            return sprintf('%s (%s)', $user->username, $user->email);
        });
    }
}