<?php namespace dektrium\user\events;

use dektrium\user\models\User;
use yii\base\Event;

/**
 * This event is used on logging in.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginEvent extends Event
{
    /**
     * @var User
     */
    protected $_identity;

    /**
     * @param User $identity
     */
    public function setIdentity(User $identity)
    {
        $this->_identity = $identity;
    }

    /**
     * @return User
     */
    public function getIdentity()
    {
        return $this->_identity;
    }
}