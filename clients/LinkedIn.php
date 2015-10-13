<?php
use dektrium\user\clients\ClientInterface;

use yii\authclient\clients\LinkedIn as BaseLinkedIn;

/*
 * @author Sam Mousa <sam@mousa.nl>
 */
class LinkedIn extends BaseLinkedIn implements ClientInterface
{

    /** @inheritdoc */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email-address'])
            ? $this->getUserAttributes()['email-address']
            : null;
    }

    /** @inheritdoc */
    public function getUsername()
    {
        return;
    }
}