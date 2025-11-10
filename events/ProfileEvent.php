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

use AlexeiKaDev\Yii2User\models\Profile;
use yii\base\Event;

/**
 * Represents an event triggered for a user profile.
 *
 * @property Profile $profile The profile model associated with the event.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ProfileEvent extends Event
{
    /**
     * @var Profile The profile model associated with this event.
     */
    private $_profile;

    // Add constructor to potentially satisfy linter
    public function __construct($profile, $config = [])
    {
        $this->_profile = $profile;
        parent::__construct($config);
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->_profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile($profile)
    {
        $this->_profile = $profile;
    }
}
