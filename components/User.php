<?php namespace dektrium\user\components;

use yii\web\User as BaseUser;

/**
 * User is the class for the "user" application component that manages the user authentication status.
 *
 * @property \dektrium\user\models\User $identity The identity object associated with the currently logged user. Null
 * is returned if the user is not logged in (not authenticated).
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends BaseUser
{
    /**
     * @inheritdoc
     */
    public $identityClass = '\dektrium\user\models\User';

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
    protected function afterLogin($identity, $cookieBased)
    {
        parent::afterLogin($identity, $cookieBased);
        if (\Yii::$app->getModule('user')->trackable) {
            $this->identity->setAttribute('login_ip', ip2long(\Yii::$app->getRequest()->getUserIP()));
            $this->identity->setAttribute('login_time', time());
            $this->identity->save(false);
        }
    }
}
