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
use yii\helpers\Url;
use yii\web\IdentityInterface;
use Yii;

/**
 * User ActiveRecord model.
 *
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $password_hash
 * @property string  $auth_key
 * @property integer $registered_from
 * @property integer $logged_in_from
 * @property integer $logged_in_at
 * @property string  $confirmation_token
 * @property integer $confirmation_sent_at
 * @property integer $confirmed_at
 * @property string  $unconfirmed_email
 * @property string  $recovery_token
 * @property integer $recovery_sent_at
 * @property integer $blocked_at
 * @property string  $role
 * @property integer $created_at
 * @property integer $updated_at
 * @property string  $confirmationUrl
 * @property boolean $isConfirmed
 * @property boolean $isConfirmationPeriodExpired
 * @property string  $recoveryUrl
 * @property boolean $isRecoveryPeriodExpired
 * @property boolean $isBlocked
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;

    const EVENT_BEFORE_REGISTER = 'before_register';
    const EVENT_AFTER_REGISTER = 'after_register';

    /**
     * @var string Plain password. Used for model validation.
     */
    public $password;

    /**
     * @var string Current user's password.
     */
    public $current_password;

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

    /**
     * @return array Connected accounts ($provider => $account)
     */
    public function getConnectedAccounts()
    {
        $connected = [];
        $accounts  = $this->accounts;
        foreach ($accounts as $account) {
            $connected[$account->provider] = $account;
        }

        return $connected;
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => \Yii::t('user', 'Username'),
            'email' => \Yii::t('user', 'Email'),
            'password' => \Yii::t('user', 'Password'),
            'created_at' => \Yii::t('user', 'Registration time'),
            'registered_from' => \Yii::t('user', 'Registered from'),
            'role' => \Yii::t('user', 'Role'),
            'unconfirmed_email' => \Yii::t('user', 'Unconfirmed email'),
            'current_password' => \Yii::t('user', 'Current password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = NULL)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /**
     * @inheritdoc
     */
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
        if ($this->password == null) {
            $this->password = Password::generate(8);
        }

        if ($this->module->confirmable) {
            $this->generateConfirmationData();
        } else {
            $this->confirmed_at = time();
        }

        if ($this->save()) {
            $this->module->mailer->sendWelcomeMessage($this);
            return true;
        }

        return false;
    }

    /**
     * This method is called at the beginning of user registration process.
     */
    protected function beforeRegister()
    {
        $this->trigger(self::EVENT_BEFORE_REGISTER);

        $this->setAttribute('registered_from', ip2long(\Yii::$app->request->userIP));

        if ($this->module->confirmable) {
            $this->generateConfirmationData();
        }
    }

    /**
     * This method is called at the end of user registration process.
     */
    protected function afterRegister()
    {
        if ($this->module->confirmable) {
            $this->module->mailer->sendConfirmationMessage($this);
        }
        $this->trigger(self::EVENT_AFTER_REGISTER);
    }

    /**
     * Registers a user.
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function register()
    {
        if (!$this->getIsNewRecord()) {
            throw new \RuntimeException('Calling "'.__CLASS__.'::register()" on existing user');
        }

        if ($this->validate()) {
            $this->beforeRegister();
            if ($this->save(false)) {
                $this->afterRegister();
                return true;
            }
        }

        return false;
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
                $this->confirmation_token = Yii::$app->getSecurity()->generateRandomString();
                $this->confirmation_sent_at = time();
                $this->save(false);
                $this->module->mailer->sendReconfirmationMessage($this);
            } else {
                $this->email = $this->unconfirmed_email;
                $this->unconfirmed_email = null;
            }

            return true;
        }

        return false;
    }

    /**
     * Resets unconfirmed email and confirmation data.
     */
    public function resetEmailUpdate()
    {
        if ($this->module->confirmable && !empty($this->unconfirmed_email)) {
            $this->unconfirmed_email = null;
            $this->confirmation_token = null;
            $this->confirmation_sent_at = null;
            $this->save(false);
        }
    }

    /**
     * Confirms a user by setting it's "confirmation_time" to actual time
     *
     * @param  bool $runValidation Whether to check if user has already been confirmed or confirmation token expired.
     * @return bool
     */
    public function confirm($runValidation = true)
    {
        if ($runValidation) {
            if ($this->getIsConfirmed()) {
                return true;
            } elseif ($this->getIsConfirmationPeriodExpired()) {
                return false;
            }
        }

        if (empty($this->unconfirmed_email)) {
            $this->confirmed_at = time();
        } else {
            $this->email = $this->unconfirmed_email;
            $this->unconfirmed_email = null;
        }

        $this->confirmation_token = null;
        $this->confirmation_sent_at = null;

        return $this->save(false);
    }

    /**
     * Generates confirmation data and re-sends confirmation instructions by email.
     *
     * @return bool
     */
    public function resend()
    {
        $this->generateConfirmationData();
        $this->save(false);

        return $this->module->mailer->sendConfirmationMessage($this);
    }

    /**
     * Generates confirmation data.
     */
    protected function generateConfirmationData()
    {
        $this->confirmation_token = Yii::$app->getSecurity()->generateRandomString();
        $this->confirmation_sent_at = time();
        $this->confirmed_at = null;
    }

    /**
     * @return string Confirmation url.
     */
    public function getConfirmationUrl()
    {
        if (is_null($this->confirmation_token)) {
            return null;
        }

        return Url::to(['/user/registration/confirm', 'id' => $this->id, 'token' => $this->confirmation_token], true);
    }

    /**
     * Verifies whether a user is confirmed or not.
     *
     * @return bool
     */
    public function getIsConfirmed()
    {
        return $this->confirmed_at !== null;
    }

    /**
     * Checks if the user confirmation happens before the token becomes invalid.
     *
     * @return bool
     */
    public function getIsConfirmationPeriodExpired()
    {
        return $this->confirmation_sent_at != null && ($this->confirmation_sent_at + $this->module->confirmWithin) < time();
    }

    /**
     * Resets password and sets recovery token to null
     *
     * @param  string $password
     * @return bool
     */
    public function resetPassword($password)
    {
        $this->password = $password;
        $this->setAttributes([
            'recovery_token'   => null,
            'recovery_sent_at' => null
        ], false);

        return $this->save(false);
    }

    /**
     * Checks if the password recovery happens before the token becomes invalid.
     *
     * @return bool
     */
    public function getIsRecoveryPeriodExpired()
    {
        return ($this->recovery_sent_at + $this->module->recoverWithin) < time();
    }

    /**
     * @return string Recovery url
     */
    public function getRecoveryUrl()
    {
        return Url::to(['/user/recovery/reset',
            'id' => $this->id,
            'token' => $this->recovery_token
        ], true);
    }

    /**
     * Generates recovery data and sends recovery message to user.
     */
    public function sendRecoveryMessage()
    {
        $this->recovery_token = Yii::$app->getSecurity()->generateRandomString();
        $this->recovery_sent_at = time();
        $this->save(false);

        return $this->module->mailer->sendRecoveryMessage($this);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to current time.
     */
    public function block()
    {
        $this->blocked_at = time();

        return $this->save(false);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to null.
     */
    public function unblock()
    {
        $this->blocked_at = null;

        return $this->save(false);
    }

    /**
     * @return bool Whether user is blocked.
     */
    public function getIsBlocked()
    {
        return !is_null($this->blocked_at);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('auth_key', Yii::$app->getSecurity()->generateRandomKey());
            $this->setAttribute('role', $this->module->defaultRole);
        }

        if (!empty($this->password)) {
            $this->setAttribute('password_hash', Password::hash($this->password));
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
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
}
