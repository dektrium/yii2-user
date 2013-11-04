<?php namespace dektrium\user\behaviors;

use dektrium\user\events\LoginEvent;
use dektrium\user\models\User;
use yii\base\Behavior;

/**
 * This behavior tracks registration and login ip addresses and last login time.
 *
 * Needs fields in database:
 *  - registration_ip integer Holds the remote ip of the registration
 *  - login_ip integer Holds the remote ip of the last login
 *  - login_time integer Holds the time of the last login
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Trackable extends Behavior
{
    /**
     * @inheritdoc
     * @return array
     */
    public function events()
    {
        return [
            User::EVENT_BEFORE_REGISTER => 'trackRegister',
            User::EVENT_AFTER_LOGIN     => 'trackLogin'
        ];
    }

    /**
     * Tracks registration ip address.
     */
    public function trackRegister()
    {
        $this->owner->setAttribute('registration_ip', ip2long(\Yii::$app->getRequest()->getUserIP()));
    }

    /**
     * Tracks login ip address and time.
     *
     * @param LoginEvent $event
     */
    public function trackLogin(LoginEvent $event)
    {
        $event->getIdentity()->setAttribute('login_ip', ip2long(\Yii::$app->getRequest()->getUserIP()));
        $event->getIdentity()->setAttribute('login_time', time());
        $event->getIdentity()->save(false);
    }
}