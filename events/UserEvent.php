<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\events;

use AlexeiKaDev\Yii2User\models\User;
use yii\base\Event;

/**
 * Represents an event triggered for a user.
 *
 * @property User $user The user model associated with the event.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserEvent extends Event
{
    /**
     * @var User The user model associated with this event.
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
     * @param User $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }
}
