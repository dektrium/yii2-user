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
use yii\web\IdentityInterface;

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
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;

    /** @var string Plain password. Used for model validation. */
    public $password;

    /** @var string Current user's password. Used for model validation. */
    public $current_password;

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
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne($this->module->manager->profileClass, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany($this->module->manager->accountClass, ['user_id' => 'id']);
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
            'username' => \Yii::t('user', 'Username'),
            'email' => \Yii::t('user', 'Email'),
            'password' => \Yii::t('user', 'Password'),
            'created_at' => \Yii::t('user', 'Registration time'),
            // TODO: add attribute labels
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
            'register'        => ['username', 'email', 'password'],
            'connect'         => ['username', 'email'],
            'create'          => ['username', 'email', 'password', 'role'],
            'update'          => ['username', 'email', 'password', 'role'],
            'update_password' => ['password', 'current_password'],
            'update_email'    => ['unconfirmed_email', 'current_password']
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
            ['password', 'required', 'on' => 'register'],
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
     * Creates a user.
     *
     * @return bool
     */
    public function create()
    {
        if (!$this->getIsNewRecord()) {
            throw new \RuntimeException('Calling "'.__CLASS__.'::create()" on existing user');
        }

        if ($this->password == null) {
            $this->password = Password::generate(8);
        }

        $this->confirmed_at = time();

        if ($this->save()) {
            $this->module->mailer->sendWelcomeMessage($this);
            return true;
        }

        return false;
    }

    /**
     * Confirms the user.
     *
     * @param  Token $token
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function confirm(Token $token)
    {
        if ($token->type != Token::TYPE_CONFIRMATION || $token->isExpired || $token->user_id != $this->id) {
            throw new \InvalidArgumentException;
        }

        $token->delete();

        return (bool) $this->updateAttributes(['confirmed_at' => time()]);
    }

    /**
     * Updates email with new one. If confirmable option is enabled, it will send confirmation message to new email.
     *
     * @return bool
     */
    public function updateEmail()
    {
        if ($this->validate()) {
            if ($this->module->confirmable) {
                // TODO: send confirmation messages
            } else {
                $this->email = $this->unconfirmed_email;
                $this->unconfirmed_email = null;
            }

            return true;
        }

        return false;
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
            $this->setAttribute('auth_key', \Yii::$app->security->generateRandomKey());
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
