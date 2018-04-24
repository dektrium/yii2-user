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

use dektrium\user\Finder;
use dektrium\user\helpers\Password;
use dektrium\user\Mailer;
use dektrium\user\Module;
use dektrium\user\traits\ModuleTrait;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;


/**
 * User ActiveRecord model.
 *
 * @property bool    $isAdmin
 * @property bool    $isBlocked
 * @property bool    $isConfirmed
 *
 * Database fields:
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $unconfirmed_email
 * @property string  $password_hash
 * @property string  $auth_key
 * @property string  $registration_ip
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_at
 * @property integer $flags
 *
 * Defined relations:
 * @property Account[] $accounts
 * @property Profile   $profile
 *
 * Dependencies:
 * @property-read Finder $finder
 * @property-read Module $module
 * @property-read Mailer $mailer
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;

    const BEFORE_CREATE   = 'beforeCreate';
    const AFTER_CREATE    = 'afterCreate';
    const BEFORE_REGISTER = 'beforeRegister';
    const AFTER_REGISTER  = 'afterRegister';
    const BEFORE_CONFIRM  = 'beforeConfirm';
    const AFTER_CONFIRM   = 'afterConfirm';

    // following constants are used on secured email changing process
    const OLD_EMAIL_CONFIRMED = 0b1;
    const NEW_EMAIL_CONFIRMED = 0b10;

    /** @var string Plain password. Used for model validation. */
    public $password;

    /** @var Profile|null */
    private $_profile;

    /** @var string Default username regexp */
    public static $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';

    /**
     * @return Finder
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFinder()
    {
        return \Yii::$container->get(Finder::className());
    }

    /**
     * @return Mailer
     * @throws \yii\base\InvalidConfigException
     */
    protected function getMailer()
    {
        return \Yii::$container->get(Mailer::className());
    }

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
        return
            (\Yii::$app->getAuthManager() && $this->module->adminPermission ?
                \Yii::$app->authManager->checkAccess($this->id, $this->module->adminPermission) : false)
            || in_array($this->username, $this->module->admins);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne($this->module->modelMap['Profile'], ['user_id' => 'id']);
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $this->_profile = $profile;
    }

    /**
     * @return Account[] Connected accounts ($provider => $account)
     */
    public function getAccounts()
    {
        $connected = [];
        $accounts  = $this->hasMany($this->module->modelMap['Account'], ['user_id' => 'id'])->all();

        /** @var Account $account */
        foreach ($accounts as $account) {
            $connected[$account->provider] = $account;
        }

        return $connected;
    }

    /**
     * Returns connected account by provider.
     * @param  string $provider
     * @return Account|null
     */
    public function getAccountByProvider($provider)
    {
        $accounts = $this->getAccounts();
        return isset($accounts[$provider])
            ? $accounts[$provider]
            : null;
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
            'last_login_at'     => \Yii::t('user', 'Last login'),
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
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            'register' => ['username', 'email', 'password'],
            'connect'  => ['username', 'email'],
            'create'   => ['username', 'email', 'password'],
            'update'   => ['username', 'email', 'password'],
            'settings' => ['username', 'email', 'password'],
        ]);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            // username rules
            'usernameTrim'     => ['username', 'trim'],
            'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            'usernameMatch'    => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameUnique'   => [
                'username',
                'unique',
                'message' => \Yii::t('user', 'This username has already been taken')
            ],

            // email rules
            'emailTrim'     => ['email', 'trim'],
            'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            'emailPattern'  => ['email', 'email'],
            'emailLength'   => ['email', 'string', 'max' => 255],
            'emailUnique'   => [
                'email',
                'unique',
                'message' => \Yii::t('user', 'This email address has already been taken')
            ],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register']],
            'passwordLength'   => ['password', 'string', 'min' => 6, 'max' => 72, 'on' => ['register', 'create']],
        ];
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_key') === $authKey;
    }

    /**
     * Creates new user account. If Module::enableGeneratingPassword is set true, this method
     * will generate password.
     *
     * @return bool
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->password = ($this->password == null && $this->module->enableGeneratingPassword) ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_CREATE);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            $this->confirm();

            $this->mailer->sendWelcomeMessage($this, null, true);
            $this->trigger(self::AFTER_CREATE);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::warning($e->getMessage());
            throw $e;
        }
    }

    /**
     * This method is used to register new user account. If Module::enableConfirmation is set true, this method
     * will generate new confirmation token and use mailer to send it to the user.
     *
     * @return bool
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->confirmed_at = $this->module->enableConfirmation ? null : time();
            $this->password     = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;

            $this->trigger(self::BEFORE_REGISTER);

            if (!$this->save()) {
                $transaction->rollBack();
                return false;
            }

            if ($this->module->enableConfirmation) {
                /** @var Token $token */
                $token = \Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
            }

            $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);
            $this->trigger(self::AFTER_REGISTER);

            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::warning($e->getMessage());
            throw $e;
        }
    }

    /**
     * Attempts user confirmation.
     *
     * @param string $code Confirmation code.
     *
     * @return boolean
     */
    public function attemptConfirmation($code)
    {
        $token = $this->finder->findTokenByParams($this->id, $code, Token::TYPE_CONFIRMATION);

        if ($token instanceof Token && !$token->isExpired) {
            $token->delete();
            if (($success = $this->confirm())) {
                \Yii::$app->user->login($this, $this->module->rememberFor);
                $message = \Yii::t('user', 'Thank you, registration is now complete.');
            } else {
                $message = \Yii::t('user', 'Something went wrong and your account has not been confirmed.');
            }
        } else {
            $success = false;
            $message = \Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.');
        }

        \Yii::$app->session->setFlash($success ? 'success' : 'danger', $message);

        return $success;
    }

    /**
     * Generates a new password and sends it to the user.
     *
     * @param string $code Confirmation code.
     *
     * @return boolean
     */
    public function resendPassword()
    {
        $this->password = Password::generate(8);
        $this->save(false, ['password_hash']);

        return $this->mailer->sendGeneratedPassword($this, $this->password);
    }

    /**
     * This method attempts changing user email. If user's "unconfirmed_email" field is empty is returns false, else if
     * somebody already has email that equals user's "unconfirmed_email" it returns false, otherwise returns true and
     * updates user's password.
     *
     * @param string $code
     *
     * @return bool
     * @throws \Exception
     */
    public function attemptEmailChange($code)
    {
        // TODO refactor method

        /** @var Token $token */
        $token = $this->finder->findToken([
            'user_id' => $this->id,
            'code'    => $code,
        ])->andWhere(['in', 'type', [Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL]])->one();

        if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Your confirmation token is invalid or expired'));
        } else {
            $token->delete();

            if (empty($this->unconfirmed_email)) {
                \Yii::$app->session->setFlash('danger', \Yii::t('user', 'An error occurred processing your request'));
            } elseif ($this->finder->findUser(['email' => $this->unconfirmed_email])->exists() == false) {
                if ($this->module->emailChangeStrategy == Module::STRATEGY_SECURE) {
                    switch ($token->type) {
                        case Token::TYPE_CONFIRM_NEW_EMAIL:
                            $this->flags |= self::NEW_EMAIL_CONFIRMED;
                            \Yii::$app->session->setFlash(
                                'success',
                                \Yii::t(
                                    'user',
                                    'Awesome, almost there. Now you need to click the confirmation link sent to your old email address'
                                )
                            );
                            break;
                        case Token::TYPE_CONFIRM_OLD_EMAIL:
                            $this->flags |= self::OLD_EMAIL_CONFIRMED;
                            \Yii::$app->session->setFlash(
                                'success',
                                \Yii::t(
                                    'user',
                                    'Awesome, almost there. Now you need to click the confirmation link sent to your new email address'
                                )
                            );
                            break;
                    }
                }
                if ($this->module->emailChangeStrategy == Module::STRATEGY_DEFAULT
                    || ($this->flags & self::NEW_EMAIL_CONFIRMED && $this->flags & self::OLD_EMAIL_CONFIRMED)) {
                    $this->email = $this->unconfirmed_email;
                    $this->unconfirmed_email = null;
                    \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your email address has been changed'));
                }
                $this->save(false);
            }
        }
    }

    /**
     * Confirms the user by setting 'confirmed_at' field to current time.
     */
    public function confirm()
    {
        $this->trigger(self::BEFORE_CONFIRM);
        $result = (bool) $this->updateAttributes(['confirmed_at' => time()]);
        $this->trigger(self::AFTER_CONFIRM);
        return $result;
    }

    /**
     * Resets password.
     *
     * @param string $password
     *
     * @return bool
     */
    public function resetPassword($password)
    {
        return (bool)$this->updateAttributes(['password_hash' => Password::hash($password)]);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to current time and regenerates auth_key.
     */
    public function block()
    {
        return (bool)$this->updateAttributes([
            'blocked_at' => time(),
            'auth_key'   => \Yii::$app->security->generateRandomString(),
        ]);
    }

    /**
     * UnBlocks the user by setting 'blocked_at' field to null.
     */
    public function unblock()
    {
        return (bool)$this->updateAttributes(['blocked_at' => null]);
    }

    /**
     * Generates new username based on email address, or creates new username
     * like "emailuser1".
     */
    public function generateUsername()
    {
        // try to use name part of email
        $username = explode('@', $this->email)[0];
        $this->username = $username;
        if ($this->validate(['username'])) {
            return $this->username;
        }

        // valid email addresses are less restricitve than our
        // valid username regexp so fallback to 'user123' if needed:
        if (!preg_match(self::$usernameRegexp, $username)) {
            $username = 'user';
        }
        $this->username = $username;

        $max = $this->finder->userQuery->max('id');

        // generate username like "user1", "user2", etc...
        do {
            $this->username = $username . ++$max;
        } while (!$this->validate(['username']));

        return $this->username;
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('auth_key', \Yii::$app->security->generateRandomString());
            if (\Yii::$app instanceof WebApplication) {
                $this->setAttribute('registration_ip', \Yii::$app->request->userIP);
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
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            if ($this->_profile == null) {
                $this->_profile = \Yii::createObject(Profile::className());
            }
            $this->_profile->link('user', $this);
        }
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
        throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
    }
}
