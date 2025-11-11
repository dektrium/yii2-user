<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\filters;

use AlexeiKaDev\Yii2User\models\User;
use Yii;

/**
 * Access rule class for simpler RBAC.
 * Allows using 'admin' role for checking User::isAdmin property.
 * @see https://github.com/dektrium/yii2-user/blob/master/docs/custom-access-control.md
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AccessRule extends \yii\filters\AccessRule
{
    /**
     * @inheritdoc
     * */
    protected function matchRole($user) // Parameter $user is typically the yii\web\User component instance
    {
        if (empty($this->roles)) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role === '?') {
                if (Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === 'admin') {
                $identity = Yii::$app->user->identity;

                if ($identity instanceof User && $identity->getIsAdmin()) { // Check against our User model
                    return true;
                }
                // Check standard RBAC roles if $user component has checkAccess method
            } elseif (!$user->isGuest && $user->can($role)) {
                return true;
            }
        }

        return false;
    }
}
