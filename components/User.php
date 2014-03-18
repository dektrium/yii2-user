<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\components;

use yii\web\User as BaseUser;

/**
 * User is the class for the "user" application component that manages the user authentication status.
 *
 * @property \dektrium\user\models\UserInterface $identity The identity object associated with the currently logged user.
 * Null is returned if the user is not logged in (not authenticated).
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends BaseUser
{
    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ['/user/auth/login'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->identityClass == null) {
            $this->identityClass = \Yii::$app->getModule('user')->factory->userClass;
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        parent::afterLogin($identity, $cookieBased, $duration);
        if (\Yii::$app->getModule('user')->trackable) {
            $this->identity->setAttribute('logged_in_from', ip2long(\Yii::$app->getRequest()->getUserIP()));
            $this->identity->setAttribute('logged_in_at', time());
            $this->identity->save(false);
        }
    }
}
