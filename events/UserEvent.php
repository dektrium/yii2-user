<?php

/*
 * This file is part of the DDMTechDev project.
 *
 * (c) DDMTechDev project <http://github.com/ddmtechdev/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ddmtechdev\user\events;

use ddmtechdev\user\models\User;
use yii\base\Event;

/**
 * @property User $model
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserEvent extends Event
{
    /**
     * @var User
     */
    private $_user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param User $form
     */
    public function setUser(User $form)
    {
        $this->_user = $form;
    }
}
