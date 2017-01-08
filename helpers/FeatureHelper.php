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

use dektrium\user\service\RegistrationService;
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
     * @var RegistrationService
     */
    protected static $registrationService;

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
     * @return bool
     */
    public static function isRegistrationEnabled()
    {
        return static::getRegistrationService()->isEnabled;
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

    /**
     * @return object|UserConfirmation
     */
    protected static function getRegistrationService()
    {
        if (static::$registrationService === null) {
            static::$registrationService = \Yii::createObject(RegistrationService::className());
        }

        return static::$registrationService;
    }
}