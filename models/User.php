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

use dektrium\user\helpers\ModuleTrait;
use dektrium\user\helpers\Password;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\log\Logger;
use yii\web\IdentityInterface;
use Yii;

/**
 * User ActiveRecord model.
 *
 * Database fields:
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $unconfirmed_email
 * @property string  $password_hash
 * @property string  $auth_key
 * @property integer $registration_ip
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $flags
 *
 * Defined relations:
 * @property Account[] $accounts
 * @property Profile   $profile
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;

    const USER_CREATE_INIT   = 'user_create_init';
    const USER_CREATE_DONE   = 'user_create_done';
    const USER_REGISTER_INIT = 'user_register_init';
    const USER_REGISTER_DONE = 'user_register_done';

    /** @var string Plain password. Used for model validation. */
    public $password;

    /**
     * @return bool Whether the user is confirmed or not.
     */
    public function getIsConfirmed()
    {
        return $this->confirmed_at != null;
    }

    /**
     * @return bool Whether the user is blocked or not.
     */
    public function getIsBlocked()
    {
        return $this->blocked_at != null;
    }

    /**
     * @return bool Whether the user is an admin or not.
     */
    public function getIsAdmin()
    {
        return in_array($this->username, $this->module->admins);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne($this->module->manager->profileClass, ['user_id' => 'id']);
    }

    /**
     * @return Account[] Connected accounts ($provider => $account)
     */
    public function getAccounts()
    {
        $connected = [];
        $accounts  = $this->hasMany($this->module->manager->accountClass, ['user_id' => 'id'])->all();

        /** @var Account $account */
        foreach ($accounts as $account) {
            $connected[$account->provider] = $account;
        }

        return $connected;
    }

    /** @inheritdoc */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /** @inheritdoc */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'username'          => \Yii::t('user', 'Username'),
            'email'             => \Yii::t('user', 'Email'),
            'registration_ip'   => \Yii::t('user', 'Registration ip'),
            'unconfirmed_email' => \Yii::t('user', 'New email'),
            'password'          => \Yii::t('user', 'Password'),
            'created_at'        => \Yii::t('user', 'Registration time'),
            'confirmed_at'      => \Yii::t('user', 'Confirmation time'),
        ];
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /** @inheritdoc */
    public function scenarios()
    {
        return [
            'register' => ['username', 'email', 'password'],
            'connect'  => ['username', 'email'],
            'create'   => ['username', 'email', 'password', 'role'],
            'update'   => ['username', 'email', 'password', 'role'],
            'settings' => ['username', 'email', 'password']
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            // username rules
            ['username', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            ['username', 'match', 'pattern' => '/^[a-zA-Z]\w+$/'],
            ['username', 'string', 'min' => 3, 'max' => 25],
            ['username', 'unique'],
            ['username', 'trim'],

            // email rules
            ['email', 'required', 'on' => ['register', 'connect', 'create', 'update', 'update_email']],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique'],
            ['email', 'trim'],

            // unconfirmed email rules
            ['unconfirmed_email', 'required', 'on' => 'update_email'],
            ['unconfirmed_email', 'unique', 'targetAttribute' => 'email', 'on' => 'update_email'],
            ['unconfirmed_email', 'email', 'on' => 'update_email'],

            // password rules
            ['password', 'required', 'on' => ['register', 'update_password']],
            ['password', 'string', 'min' => 6, 'on' => ['register', 'update_password', 'create']],

            // current password rules
            ['current_password', 'required', 'on' => ['update_email', 'update_password']],
            ['current_password', function ($attr) {
                if (!empty($this->$attr) && !Password::validate($this->$attr, $this->password_hash)) {
                    $this->addError($attr, \Yii::t('user', 'Current password is not valid'));
                }
            }, 'on' => ['update_email', 'update_password']],
        ];
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_key') == $authKey;
    }


    /**
     * This method is used to create new user account. If password is not set, this method will generate new 8-char
     * password. After saving user to database, this method uses mailer component to send credentials
     * (username and password) to user via email.
     *
     * @return bool
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmed_at = time();

        if ($this->password == null) {
            $this->password = Password::generate(8);
        }

        $this->trigger(self::USER_CREATE_INIT);

        if ($this->save()) {
            $this->trigger(self::USER_CREATE_DONE);
            $this->module->mailer->sendWelcomeMessage($this);
            \Yii::getLogger()->log('User has been created', Logger::LEVEL_INFO);
            return true;
        }

        \Yii::getLogger()->log('An error occurred while creating user account', Logger::LEVEL_ERROR);

        return false;
    }

    /**
     * This method is used to register new user account. If Module::enableConfirmation is set true, this method
     * will generate new confirmation token and use mailer to send it to the user. Otherwise it will log the user in.
     * If Module::enableGeneratingPassword is set true, this method will generate new 8-char password. After saving user
     * to database, this method uses mailer component to send credentials (username and password) to user via email.
     *
     * @return bool
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        if ($this->module->enableConfirmation == false) {
            $this->confirmed_at = time();
        }

        if ($this->module->enableGeneratingPassword) {
            $this->password = Password::generate(8);
        }

        $this->trigger(self::USER_REGISTER_INIT);

        if ($this->save()) {
            $this->trigger(self::USER_REGISTER_DONE);
            if ($this->module->enableConfirmation) {
                $token = $this->module->manager->createToken(['type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
                $this->module->mailer->sendConfirmationMessage($this, $token);
                \Yii::$app->session->setFlash('user.confirmation_sent');
            } else {
                \Yii::$app->session->setFlash('user.registration_finished');
                \Yii::$app->user->login($this);
            }
            if ($this->module->enableGeneratingPassword) {
                $this->module->mailer->sendWelcomeMessage($this);
                \Yii::$app->session->setFlash('user.password_generated');
            }
            \Yii::getLogger()->log('User has been registered', Logger::LEVEL_INFO);
            return true;
        }

        \Yii::getLogger()->log('An error occurred while registering user account', Logger::LEVEL_ERROR);

        return false;
    }

    /**
     * This method attempts user confirmation. It uses model manager to find token with given code and if it is expired
     * or does not exist, this method will throw exception.
     *
     * If confirmation passes it will return true, otherwise it will return false.
     *
     * @param  string  $code Confirmation code.
     * @return boolean
     */
    public function attemptConfirmation($code)
    {
        $token = $this->module->manager->findToken($this->id, $code, Token::TYPE_CONFIRMATION);

        if ($token === null || $token->isExpired) {
            return false;
        }

        $token->delete();

        $this->confirmed_at = time();

        \Yii::getLogger()->log('User has been confirmed', Logger::LEVEL_INFO);

        return $this->save(false);
    }

    /**
     * This method attempts changing user email. If user's "unconfirmed_email" field is empty is returns false, else if
     * somebody already has email that equals user's "unconfirmed_email" it returns false, otherwise returns true and
     * updates user's password.
     *
     * @param  string $code
     * @return bool
     * @throws \Exception
     */
    public function attemptEmailChange($code)
    {
        $token = $this->module->manager->findToken($this->id, $code, Token::TYPE_CONFIRM_NEW_EMAIL);

        if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
            return false;
        }

        $token->delete();

        if (empty($this->unconfirmed_email)) {
            return false;
        } else if (static::find()->where(['email' => $this->unconfirmed_email])->exists() == false) {
            $status = true;
            $this->email = $this->unconfirmed_email;
        } else {
            $status = false;
        }

        $this->unconfirmed_email = null;
        $this->save(false);

        return $status;
    }

    /**
     * Resets password.
     *
     * @param  string $password
     * @return bool
     */
    public function resetPassword($password)
    {
        return (bool) $this->updateAttributes(['password_hash' => Password::hash($password)]);
    }

    /**
     * Confirms the user by setting 'blocked_at' field to current time.
     */
    public function confirm()
    {
        return (bool) $this->updateAttributes(['confirmed_at' => time()]);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to current time.
     */
    public function block()
    {
        return (bool) $this->updateAttributes(['blocked_at' => time()]);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to null.
     */
    public function unblock()
    {
        return (bool) $this->updateAttributes(['blocked_at' => null]);
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('auth_key', \Yii::$app->security->generateRandomString());
            if (\Yii::$app instanceof \yii\web\Application) {
                $this->setAttribute('registration_ip', ip2long(Yii::$app->request->userIP));
            }
        }

        if (!empty($this->password)) {
            $this->setAttribute('password_hash', Password::hash($this->password));
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $profile = $this->module->manager->createProfile([
                'user_id'        => $this->id,
                'gravatar_email' => $this->email
            ]);
            $profile->save(false);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /** @inheritdoc */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
}
