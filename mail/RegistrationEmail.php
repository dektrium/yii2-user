<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\mail;

use dektrium\user\models\User;
use yii\base\Object;

/**
 * Registration email class.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationEmail extends Object
{
    /**
     * @var bool
     */
    private $_isPasswordShown = false;

    /**
     * @var string|null
     */
    private $_confirmationLink = null;

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
     * @param  User $user
     * @return RegistrationEmail
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPasswordShown()
    {
        return $this->_isPasswordShown;
    }

    /**
     * @param bool $isPasswordShown
     * @return RegistrationEmail
     */
    public function setIsPasswordShown($isPasswordShown)
    {
        $this->_isPasswordShown = $isPasswordShown;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getConfirmationLink()
    {
        return $this->_confirmationLink;
    }

    /**
     * @param  null|string $confirmationLink
     * @return RegistrationEmail
     */
    public function setConfirmationLink($confirmationLink)
    {
        $this->_confirmationLink = $confirmationLink;
        return $this;
    }

    /**
     * RegistrationEmail constructor.
     * @param User $user
     */
    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);
        $this->setUser($user);
    }
}
