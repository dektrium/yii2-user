<?php

declare(strict_types=1);

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\models;

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\helpers\Password;
use AlexeiKaDev\Yii2User\Mailer;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;

/**
 * User ActiveRecord model.
 *
 * @property bool    $isAdmin
 * @property bool    $isBlocked
 * @property bool    $isConfirmed
 *
 * Database fields:
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string|null $unconfirmed_email
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $registration_ip
 * @property int|null $confirmed_at
 * @property int|null $blocked_at
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $last_login_at
 * @property int $flags
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

    public const BEFORE_CREATE = 'beforeCreate';

    public const AFTER_CREATE = 'afterCreate';

    public const BEFORE_REGISTER = 'beforeRegister';

    public const AFTER_REGISTER = 'afterRegister';

    public const BEFORE_CONFIRM = 'beforeConfirm';

    public const AFTER_CONFIRM = 'afterConfirm';

    // following constants are used on secured email changing process
    public const OLD_EMAIL_CONFIRMED = 0b1;

    public const NEW_EMAIL_CONFIRMED = 0b10;

    /** @var string|null Plain password. Used for model validation. */
    public ?string $password = null;

    /** @var Profile|null Referenced by $this->getProfile() */
    private ?Profile $_profile = null;

    /** @var string Default username regexp */
    public static string $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';

    /** @var int Maximum username length */
    private const USERNAME_MAX_LENGTH = 255;

    /**
     * @return Finder
     * @throws InvalidConfigException
     */
    protected function getFinder(): Finder
    {
        return Yii::$container->get(Finder::class);
    }

    /**
     * @return Mailer
     * @throws InvalidConfigException
     */
    protected function getMailer(): Mailer
    {
        return Yii::$container->get(Mailer::class);
    }

    /**
     * @return bool Whether the user is confirmed or not.
     */
    public function getIsConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    /**
     * @return bool Whether the user is blocked or not.
     */
    public function getIsBlocked(): bool
    {
        return $this->blocked_at !== null;
    }

    /**
     * @return bool Whether the user is an admin or not.
     */
    public function getIsAdmin(): bool
    {
        $authManager = Yii::$app->getAuthManager();
        $isAdminByRbac = false;

        if ($authManager !== null && $this->module->adminPermission !== null) {
            $isAdminByRbac = $authManager->checkAccess($this->getId(), $this->module->adminPermission);
        }

        $isAdminByUsername = !empty($this->module->admins) && in_array($this->username, $this->module->admins, true);

        return $isAdminByRbac || $isAdminByUsername;
    }

    /**
     * @return ActiveQuery
     */
    public function getProfile(): ActiveQuery
    {
        return $this->hasOne($this->module->modelMap['Profile'], ['user_id' => 'id']);
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile): void
    {
        $this->_profile = $profile;
    }

    /**
     * @return Account[] Connected accounts ($provider => $account)
     */
    public function getAccounts(): array
    {
        $connected = [];
        /** @var Account[] $accounts */
        $accounts = $this->hasMany($this->module->modelMap['Account'], ['user_id' => 'id'])->all();

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
    public function getAccountByProvider(string $provider): ?Account
    {
        /** @var Account|null $account */
        $account = $this->hasMany($this->module->modelMap['Account'], ['user_id' => 'id'])
                        ->andWhere(['provider' => $provider])
                        ->one();

        return $account;
    }

    /** @inheritdoc */
    public function getId(): int
    {
        return (int)$this->getAttribute('id');
    }

    /** @inheritdoc */
    public function getAuthKey(): ?string
    {
        return $this->getAttribute('auth_key');
    }

    /** @inheritdoc */
    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'registration_ip' => Yii::t('user', 'Registration ip'),
            'unconfirmed_email' => Yii::t('user', 'New email'),
            'password' => Yii::t('user', 'Password'),
            'created_at' => Yii::t('user', 'Registration time'),
            'last_login_at' => Yii::t('user', 'Last login'),
            'confirmed_at' => Yii::t('user', 'Confirmation time'),
        ];
    }

    /** @inheritdoc */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /** @inheritdoc */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();

        return ArrayHelper::merge($scenarios, [
            'register' => ['username', 'email', 'password'],
            'connect' => ['username', 'email'],
            'create' => ['username', 'email', 'password'],
            'update' => ['username', 'email', 'password'],
            'settings' => ['username', 'email', 'password'],
        ]);
    }

    /** @inheritdoc */
    public function rules(): array
    {
        return [
            // username rules
            'usernameTrim' => ['username', 'trim'],
            'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            'usernameMatch' => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => self::USERNAME_MAX_LENGTH],
            'usernameUnique' => [
                'username',
                'unique',
                'message' => Yii::t('user', 'This username has already been taken')
            ],

            // email rules
            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            'emailPattern' => ['email', 'email'],
            'emailLength' => ['email', 'string', 'max' => 255],
            'emailUnique' => [
                'email',
                'unique',
                'message' => Yii::t('user', 'This email address has already been taken'),
            ],
            'emailUnconfirmedUnique' => [
                'unconfirmed_email',
                'unique',
                'message' => Yii::t('user', 'This email address has already been taken'),
                'when' => fn ($model) => (bool)$model->unconfirmed_email
            ],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register']],
            'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72, 'on' => ['register', 'create']],
        ];
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey): ?bool
    {
        return $this->getAttribute('auth_key') === $authKey;
    }

    /**
     * Creates new user account. It includes password generation (if password is not provided) and sending email
     * with credentials.
     * @return bool True if user was successfully created
     */
    public function create(): bool
    {
        if ($this->getIsNewRecord() === false) {
            throw new \RuntimeException('Calling "create()" on existing user');
        }

        $transaction = $this->getDb()->beginTransaction();

        try {
            $this->password = ($this->password === null && $this->module->enableGeneratingPassword)
                ? Password::generate(8)
                : $this->password;

            $this->trigger(self::BEFORE_CREATE);

            if (!$this->save()) {
                $transaction->rollBack();

                return false;
            }

            $this->trigger(self::AFTER_CREATE);

            // create profile
            $profile = Yii::createObject(Profile::class);
            $profile->link('user', $this);

            $transaction->commit();

            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::warning($e->getMessage());

            throw $e;
        }
    }

    /**
     * Attempts to confirm user account by code.
     * @param string $code Confirmation code.
     * @return bool True if confirmation was successful.
     */
    public function attemptConfirmation(string $code): bool
    {
        /** @var Token $token */
        $token = $this->finder->findToken([
            'user_id' => $this->id,
            'code'    => $code,
            'type'    => TokenType::CONFIRMATION,
        ])->one();

        if ($token === null || $token->isExpired || $this->isConfirmed) {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.')
            );
        } else {
            $token->delete();
            $this->confirm(); // Calls the refactored confirm() method
            Yii::$app->user->login($this, $this->module->rememberFor);
            Yii::$app->session->setFlash('success', Yii::t('user', 'Thank you, registration is now complete.'));
        }

        return false; // Original method returned false in most cases, keeping similar for now if used elsewhere.
                      // Service method has clearer boolean return.
    }

    /**
     * Confirms the user by setting 'confirmed_at' field to current time.
     * @return bool True if user was successfully confirmed.
     */
    public function confirm(): bool
    {
        // BEFORE_CONFIRM and AFTER_CONFIRM events are expected to be handled by the caller (e.g., UserConfirmationService)
        $this->confirmed_at = time();
        return (bool)$this->save(false, ['confirmed_at']);
    }

    /**
     * Resends the password recovery email.
     * @return bool True if password recovery email was sent successfully.
     */
    public function resendPassword(): bool
    {
        if (!$this->module->enablePasswordRecovery) {
            return false;
        }

        /** @var Token $token */
        $token = Yii::createObject(['class' => Token::class, 'type' => TokenType::RECOVERY]);
        $token->link('user', $this);

        return true;
    }

    /**
     * Attempts to change user's email address by code.
     * @param string $code Confirmation code for email change.
     * @return bool True if email change was successful.
     */
    public function attemptEmailChange(string $code): bool
    {
        /** @var Token|null $token */
        $token = $this->getFinder()->findTokenByParams($this->id, $code, TokenType::CONFIRM_NEW_EMAIL->value);

        if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'Your confirmation token is invalid or expired')
            );

            return false;
        }

        $token->delete();

        if (empty($this->unconfirmed_email)) {
            return false;
        }

        $this->email = $this->unconfirmed_email;
        $this->unconfirmed_email = null;

        if (!$this->updateAttributes(['email', 'unconfirmed_email'])) {
            return false;
        }

        if ($this->module->emailChangeStrategy === Module::STRATEGY_SECURE) {
            $this->flags &= ~self::NEW_EMAIL_CONFIRMED;
            $this->flags &= ~self::OLD_EMAIL_CONFIRMED;
            $this->save(false, ['flags']);

            /** @var Token $tokenOld */
            $tokenOld = Yii::createObject(['class' => Token::class, 'type' => TokenType::CONFIRM_OLD_EMAIL]);
            $tokenOld->link('user', $this);
        }

        return true;
    }

    /**
     * Resets user's password.
     * @param string $password The new password.
     * @return bool True if password was reset successfully.
     */
    public function resetPassword(string $password): bool
    {
        return (bool)$this->updateAttributes(['password_hash' => Password::hash($password)]);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to current time.
     * @return bool True if user was successfully blocked.
     */
    public function block(): bool
    {
        return (bool)$this->updateAttributes([
            'blocked_at' => time(),
            'auth_key' => Yii::$app->security->generateRandomString(),
        ]);
    }

    /**
     * Unblocks the user by setting 'blocked_at' field to null.
     * @return bool True if user was successfully unblocked.
     */
    public function unblock(): bool
    {
        return (bool)$this->updateAttributes(['blocked_at' => null]);
    }

    /**
     * Generates new username based on email address, or a random string if email is unavailable.
     * @param bool $generateRandomString Whether to generate random string if username generation fails.
     */
    public function generateUsername(bool $generateRandomString = false): void
    {
        if ($generateRandomString) {
            // try to generate username the random string
            $randomString = md5(uniqid((string)mt_rand(), true));
            $this->username = substr(strtolower($randomString), 0, self::USERNAME_MAX_LENGTH);
        } else {
            // try to generate username from email
            if (empty($this->email)) {
                $this->generateUsername(true);

                return;
            }
            $username = explode('@', $this->email, 2)[0];
            // make sure username meets the requirements
            $username = preg_replace('/[^\w\.\-_@]+/u', '', $username);

            if (mb_strlen($username) > self::USERNAME_MAX_LENGTH) {
                $username = mb_substr($username, 0, self::USERNAME_MAX_LENGTH);
            }

            if (empty($username)) {
                $this->generateUsername(true);

                return;
            }
            $this->username = $username;
        }
    }

    /** @inheritdoc */
    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->setAttribute('auth_key', Yii::$app->security->generateRandomString());

            if (Yii::$app instanceof WebApplication) {
                $this->setAttribute('registration_ip', Yii::$app->request->userIP);
            }
        }

        if (!empty($this->password)) {
            $this->setAttribute('password_hash', Password::hash($this->password));
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->_profile?->save(false);
        }
    }

    /** @inheritdoc */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /** @inheritdoc */
    public static function findIdentity($id): ?static
    {
        return static::findOne($id);
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null): ?static
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
}
