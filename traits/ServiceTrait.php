<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\traits;

use dektrium\user\service\UserConfirmation;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
trait ServiceTrait
{
    /**
     * @var UserConfirmation
     */
    protected $userConfirmationService;

    /**
     * @return UserConfirmation|object
     */
    protected function getUserConfirmationService()
    {
        if (!$this->userConfirmationService) {
            $this->userConfirmationService = \Yii::createObject(UserConfirmation::className());
        }

        return $this->userConfirmationService;
    }
}