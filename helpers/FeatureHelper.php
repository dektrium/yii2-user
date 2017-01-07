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

use dektrium\user\service\UserConfirmation;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class FeatureHelper
{
    /**
     * @var UserConfirmation
     */
    protected static $userConfirmation;

    /**
     * Whether admin approval is enabled.
     *
     * @return bool
     */
    public static function isAdminApprovalEnabled()
    {
        return static::getUserConfirmation()->isEnabled && static::getUserConfirmation()->isAdminApprovalEnabled;
    }

    /**
     * Whether email confirmation is enabled.
     *
     * @return bool
     */
    public static function isEmailConfirmationEnabled()
    {
        return static::getUserConfirmation()->isEnabled && static::getUserConfirmation()->isConfirmationByEmailEnabled;
    }

    /**
     * @return object|UserConfirmation
     */
    protected static function getUserConfirmation()
    {
        if (static::$userConfirmation === null) {
            static::$userConfirmation = \Yii::createObject(UserConfirmation::className());
        }

        return static::$userConfirmation;
    }
}