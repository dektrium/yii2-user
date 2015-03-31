<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\models;

use dektrium\user\Module;
use dektrium\user\Finder;
use Yii;
use yii\authclient\ClientInterface as BaseClientInterface;
use dektrium\user\clients\ClientInterface;
use yii\db\ActiveRecord;

/**
 * @property integer $id          Id
 * @property integer $user_id     User id, null if account is not bind to user
 * @property string  $provider    Name of service
 * @property string  $client_id   Account id
 * @property string  $data        Account properties returned by social network (json encoded)
 * @property string  $decodedData Json-decoded properties
 * @property User    $user        User that this account is connected for.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Account extends ActiveRecord
{
    /** @var Module */
    protected $module;

    /** @var Finder */
    protected static $finder;
    
    /** @var */
    private $_data;

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule('user');
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%social_account}}';
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @return bool Whether this social account is connected to user.
     */
    public function getIsConnected()
    {
        return $this->user_id != null;
    }

    /**
     * @return mixed Json decoded properties.
     */
    public function getDecodedData()
    {
        if ($this->_data == null) {
            $this->_data = json_decode($this->data);
        }

        return $this->_data;
    }
    
    /**
     * Tries to find an account and then connect that account with current user.
     * 
     * @param BaseClientInterface $client
     */
    public static function connectWithUser(BaseClientInterface $client)
    {
        if (\Yii::$app->user->isGuest) {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Something went wrong'));
            return;
        }
        
        $account = static::fetchAccount($client);
        
        if ($account->user === null) {
            $account->link('user', Yii::$app->user->identity);
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account has been connected'));
        } else {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'This account has already been connected to another user'));
        }
    }
    
    /**
     * At first it tries to find existing account model using data provided by
     * client. If account has not been found it is created.
     * 
     * If client is instance of "dektrium\clients\ClientInterface" and account
     * has no connected user, it will try to create new user.
     * 
     * @param  BaseClientInterface $client
     * @return Account
     */
    public static function createFromClient(BaseClientInterface $client)
    {
        $account = static::fetchAccount($client);

        if ($account->user === null && $client instanceof ClientInterface) {
            $user = static::fetchUser($client);
            if ($user instanceof User) {
                $account->link('user', $user);
            }
        }
        
        return $account;
    }
    
    /**
     * Tries to find account, otherwise creates new account.
     * @return Account
     */
    protected static function fetchAccount(BaseClientInterface $client)
    {
        $account = static::getFinder()->findAccountByClient($client);
        
        if (null === $account) {
            $account = \Yii::createObject([
                'class'      => static::className(),
                'provider'   => $client->getId(),
                'client_id'  => $client->getUserAttributes()['id'],
                'data'       => json_encode($client->getUserAttributes()),
            ]);
            $account->save(false);
        }
        
        return $account;
    }
    
    /**
     * Tries to find user or create a new one.
     * 
     * @param  ClientInterface $client
     * @return User|boolean False when can't create user.
     */
    protected static function fetchUser(ClientInterface $client)
    {
        $user = static::getFinder()->findUserByEmail($client->getEmail());
        
        if (null !== $user) {
            return $user;
        }
        
        $user = Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'connect',
            'username' => $client->getUsername(),
            'email'    => $client->getEmail(),
        ]);
        
        if (!$user->validate(['email'])) {
            return false;
        }
        
        if (!$user->validate(['username'])) {
            $user->username = null;
        }
        
        return $user->create() ? $user : false;
    }
    
    /**
     * @return Finder
     */
    protected static function getFinder()
    {
        if (static::$finder === null) {
            static::$finder = Yii::$container->get(Finder::className());
        }
        
        return static::$finder;
    }
}