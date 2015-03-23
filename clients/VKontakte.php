<?php

/* 
 * This file is part of the Dektrium project
 * 
 * (c) Dektrium project <http://github.com/dektrium>
 * 
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user\clients;

use yii\authclient\clients\VKontakte as BaseVKontakte;

/**
 * Improved version of VKontakte client that grabs user's email address.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class VKontakte extends BaseVKontakte implements ClientInterface
{
    /** @inheritdoc */
    public $scope = 'email';
    
    /** @inheritdoc */
    public function getEmail()
    {
        return $this->getAccessToken()->getParam('email');
    }
    
    /** @inheritdoc */
    public function getUsername()
    {
        if (isset($this->getUserAttributes()['screen_name'])) {
            return $this->getUserAttributes()['screen_name'];
        } else {
            return null;
        }
    }
}