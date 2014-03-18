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

use dektrium\user\helpers\Password;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\helpers\Url;

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
class User extends ActiveRecord implements UserInterface
{
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
     * @var string Verification code.
     */
    public $verify_code;

    /**
     * @var \dektrium\user\Module
     */
    private $_module;

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getProfile()
    {
        return $this->hasOne($this->_module->factory->profileClass, ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function createQuery($config = [])
    {
        $config['modelClass'] = get_called_class();

        return new UserQuery($config);
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
            'register'        => ['username', 'email', 'password', 'verify_code'],
            'short_register'  => ['username', 'email', 'verify_code'],
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
            ['username', 'required', 'on' => ['register', 'short_register', 'create', 'update']],
            ['username', 'match', 'pattern' => '/^[a-zA-Z]\w+$/'],
            ['username', 'string', 'min' => 3, 'max' => 25],
            ['username', 'unique'],

            // email rules
            ['email', 'required', 'on' => ['register', 'short_register', 'create', 'update', 'update_email']],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique'],

            // unconfirmed email rules
            ['unconfirmed_email', 'required', 'on' => 'update_email'],
            ['unconfirmed_email', 'unique', 'targetAttribute' => 'email', 'on' => 'update_email'],
            ['unconfirmed_email', 'email', 'on' => 'update_email'],

            // password rules
            ['password', 'required', 'on' => ['register', 'create']],
            ['password', 'string', 'min' => 6, 'on' => ['register', 'update_password', 'create']],

            // current password rules
            ['current_password', 'required', 'on' => ['update_email', 'update_password']],
            ['current_password', function ($attr) {
                if (!empty($this->$attr) && !Password::validate($this->$attr, $this->password_hash)) {
                    $this->addError($attr, \Yii::t('user', 'Current password is not valid'));
                }
            }, 'on' => ['update_email', 'update_password']],

            // captcha
            ['verify_code', 'captcha', 'captchaAction' => 'user/default/captcha', 'on' => ['register'],
                'skipOnEmpty' => !in_array('register', $this->getModule()->captcha)]
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
        return static::find($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token)
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
     * This method is called at the beginning of user registration process.
     */
    protected function beforeRegister()
    {
        $this->trigger(self::EVENT_BEFORE_REGISTER);
        if ($this->scenario == 'short_register') {
            $this->password = Password::generate(8);
        }
        if ($this->_module->trackable) {
            $this->setAttribute('registered_from', ip2long(\Yii::$app->request->userIP));
        }
        if ($this->_module->confirmable) {
            $this->generateConfirmationData();
        }
    }

    /**
     * This method is called at the end of user registration process.
     */
    protected function afterRegister()
    {
        if ($this->scenario == 'short_register') {
            $this->sendMessage($this->email, \Yii::t('user', 'Welcome to {sitename}', ['sitename' => \Yii::$app->name]),
                'welcome', ['user' => $this, 'password' => $this->password]
            );
        }
        if ($this->_module->confirmable) {
            $this->sendMessage($this->email, \Yii::t('user', 'Please confirm your account'),
                'confirmation',	['user' => $this]
            );
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
            $this->setAttribute('password_hash', Password::hash($this->password));
            $this->setAttribute('auth_key', Security::generateRandomKey());
            $this->setAttribute('role', $this->getModule()->defaultRole);
            if ($this->save(false)) {
                $profile = $this->_module->factory->createProfile([
                    'user_id' => $this->id,
                    'gravatar_email' => $this->email
                ]);
                $profile->save(false);
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
            if ($this->getModule()->confirmable) {
                $this->confirmation_token = Security::generateRandomKey();
                $this->confirmation_sent_at = time();
                $this->save(false);
                $this->sendMessage($this->unconfirmed_email, \Yii::t('user', 'Please confirm your email'), 'reconfirmation', ['user' => $this]);
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
        if ($this->getModule()->confirmable && !empty($this->unconfirmed_email)) {
            $this->unconfirmed_email = null;
            $this->confirmation_token = null;
            $this->confirmation_sent_at = null;
            $this->save(false);
        }
    }

    /**
     * Updates user's password.
     *
     * @return bool
     */
    public function updatePassword()
    {
        if ($this->validate()) {
            $this->password_hash = Password::hash($this->password);

            return $this->save(false);
        }

        return false;
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

        return $this->sendMessage($this->email, \Yii::t('user', 'Please confirm your account'), 'confirmation', ['user' => $this]);
    }

    /**
     * Generates confirmation data.
     */
    protected function generateConfirmationData()
    {
        $this->confirmation_token = Security::generateRandomKey();
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

        return Url::toRoute(['/user/registration/confirm', 'id' => $this->id, 'token' => $this->confirmation_token], true);
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
        return $this->confirmation_sent_at != null && ($this->confirmation_sent_at + $this->getModule()->confirmWithin) < time();
    }

    /**
     * Resets password and sets recovery token to null
     *
     * @param  string $password
     * @return bool
     */
    public function resetPassword($password)
    {
        $this->setAttributes([
            'password_hash'    => Password::hash($password),
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
        return ($this->recovery_sent_at + $this->getModule()->recoverWithin) < time();
    }

    /**
     * @return string Recovery url
     */
    public function getRecoveryUrl()
    {
        return Url::toRoute(['/user/recovery/reset',
            'id' => $this->id,
            'token' => $this->recovery_token
        ], true);
    }

    /**
     * Generates recovery data and sends recovery message to user.
     */
    public function sendRecoveryMessage()
    {
        $this->recovery_token = Security::generateRandomKey();
        $this->recovery_sent_at = time();
        $this->save(false);

        return $this->sendMessage($this->email, \Yii::t('user', 'Please complete password reset'), 'recovery', ['user' => $this]);
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
     * @return null|\dektrium\user\Module
     */
    protected function getModule()
    {
        if ($this->_module == null) {
            $this->_module = \Yii::$app->getModule('user');
        }

        return $this->_module;
    }

    /**
     * Sends message.
     *
     * @param $to
     * @param string $subject
     * @param string $view
     * @param array  $params
     *
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, $params)
    {
        $mail = \Yii::$app->getMail();
        $mail->viewPath = $this->getModule()->emailViewPath;

        if (empty(\Yii::$app->getMail()->messageConfig['from'])) {
            $mail->messageConfig['from'] = 'no-reply@example.com';
        }

        return $mail->compose($view, $params)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}
