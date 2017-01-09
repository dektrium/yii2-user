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
use dektrium\user\service\ConfirmationService;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class FeatureHelper
{
    /**
     * @var ConfirmationService
     */
    protected static $confirmationService;

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
        return static::getConfirmationService()->isEnabled && static::getConfirmationService()->isAdminApprovalEnabled;
    }

    /**
     * Whether email confirmation is enabled.
     *
     * @return bool
     */
    public static function isEmailConfirmationEnabled()
    {
        return static::getConfirmationService()->isEnabled && static::getConfirmationService()->isEmailConfirmationEnabled;
    }

    /**
     * @return bool
     */
    public static function isRegistrationEnabled()
    {
        return static::getRegistrationService()->isEnabled;
    }

    /**
     * @return object|ConfirmationService
     */
    protected static function getConfirmationService()
    {
        if (static::$confirmationService === null) {
            static::$confirmationService = \Yii::createObject(ConfirmationService::className());
        }

        return static::$confirmationService;
    }

    /**
     * @return object|ConfirmationService
     */
    protected static function getRegistrationService()
    {
        if (static::$registrationService === null) {
            static::$registrationService = \Yii::createObject(RegistrationService::className());
        }

        return static::$registrationService;
    }
}