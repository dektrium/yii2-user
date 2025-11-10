<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\models;

use AlexeiKaDev\Yii2User\clients\ClientInterface as UserModuleClientInterface;
use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\models\query\AccountQuery;
use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use Yii;
use yii\authclient\ClientInterface as BaseClientInterface;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * @property int $id Id
 * @property int|null $user_id User id, null if account is not bind to user
 * @property string $provider Name of service
 * @property string $client_id Account id
 * @property string|null $data Account properties returned by social network (json encoded)
 * @property mixed $decodedData Json-decoded properties
 * @property string|null $code
 * @property int|null $created_at
 * @property string|null $email
 * @property string|null $username
 *
 * @property User|null $user User that this account is connected for.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Account extends ActiveRecord
{
    use ModuleTrait;

    /** @var Finder|null */
    protected $finder = null;

    /** @var mixed Decoded JSON data from the $data attribute */
    private $_data = null;

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName()
    {
        return '{{%social_account}}';
    }

    /**
     * @return ActiveQuery
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
        return $this->user_id !== null;
    }

    /**
     * @return mixed Json decoded properties.
     */
    public function getDecodedData()
    {
        if ($this->_data === null && $this->data !== null) {
            try {
                $this->_data = Json::decode($this->data);
            } catch ($e) {
                // Handle or log error if $this->data is not valid JSON
                $this->_data = []; // Default to empty array or handle as error
                Yii::warning(
                    "Failed to decode social account data for account ID {$this->id}: " . $e->getMessage(),
                    __METHOD__
                );
            }
        }

        return $this->_data;
    }

    /**
     * Returns connect url.
     * Uses SHA-256 for secure hashing instead of deprecated MD5.
     * @return string
     */
    public function getConnectUrl()
    {
        $code = Yii::$app->security->generateRandomString();
        $this->updateAttributes(['code' => hash('sha256', $code)]);

        return Url::to(['/user/registration/connect', 'code' => $code]);
    }

    /**
     * Connects current social account with user.
     * @param User $user
     * @return bool
     */
    public function connect($user)
    {
        return (bool)$this->updateAttributes([
            'username' => null,
            'email' => null,
            'code' => null,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @return AccountQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(AccountQuery::class, [static::class]);
    }

    /**
     * Creates an Account instance from a client.
     * @param BaseClientInterface $client The auth client instance.
     * @return Account The created Account model.
     * @throws \yii\base\InvalidConfigException
     */
    public static function create($client)
    {
        $attributes = $client->getUserAttributes();
        /** @var Account $account */
        $account = Yii::createObject([
            'class' => static::class,
            'provider' => $client->getId(),
            'client_id' => $attributes['id'] ?? null,
            'data' => Json::encode($attributes),
        ]);

        if ($client instanceof UserModuleClientInterface) {
            $account->setAttributes([
                'username' => $client->getUsername(),
                'email' => $client->getEmail(),
            ], false);
        }

        // Try to connect account to existing user by email
        if ($account->email !== null) {
            $user = static::getFinder()->findUserByEmail($account->email);

            if ($user instanceof User) {
                $account->user_id = $user->id;
            }
        }

        // If user is not found by email, but username is available from social network,
        // and we want to connect by username (optional logic)
        if ($account->user_id === null && $account->username !== null) {
            $user = static::getFinder()->findUserByUsername($account->username);

            if ($user instanceof User) {
                $account->user_id = $user->id;
            }
        }

        $account->save(false);

        return $account;
    }

    /**
     * Tries to find an account and then connect that account with current user.
     * @param BaseClientInterface $client
     * @throws \yii\base\InvalidConfigException
     */
    public static function connectWithUser($client)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'Something went wrong'));

            return;
        }

        $currentLoggedInUser = Yii::$app->user->identity;

        if (!$currentLoggedInUser instanceof User) {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'Current user is not valid.'));

            return;
        }

        $account = static::fetchAccount($client);

        if ($account->user_id === null) {
            $account->link('user', $currentLoggedInUser);
            Yii::$app->session->setFlash('success', Yii::t('user', 'Your account has been connected'));
        } else {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'This account has already been connected to another user')
            );
        }
    }

    /**
     * Tries to find account, otherwise creates new account.
     * @param BaseClientInterface $client
     * @return Account
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchAccount($client)
    {
        $finder = static::getFinder();
        $account = $finder->findAccount()->byClient($client)->one();

        if ($account === null) {
            $attributes = $client->getUserAttributes();
            /** @var Account $account */
            $account = Yii::createObject([
                'class' => static::class,
                'provider' => $client->getId(),
                'client_id' => $attributes['id'] ?? null,
                'data' => Json::encode($attributes),
            ]);
            $account->save(false);
        }

        return $account;
    }

    /**
     * Tries to find user or create a new one (original Dektrium logic was slightly different here,
     * this is a simplified version focusing on connection, actual user creation is handled by RegistrationController).
     * This method primarily tries to find an existing user by email to link the social account.
     * @param Account $account
     * @return User|false
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchUser($account)
    {
        if ($account->email !== null) {
            $user = static::getFinder()->findUserByEmail($account->email);

            if ($user instanceof User) {
                return $user;
            }
        }

        // Original dektrium/yii2-user Account::create also tried to create a user if not found.
        // This logic is usually handled by the RegistrationController when a new social user signs up.
        // For simplicity in this Account model context, if user is not found by email, we don't create one here.
        // The create() method above will attempt to link if user exists, otherwise RegistrationController handles new social user.
        // If you need to create user here, it would require more logic from original dektrium's Account::create() method.

        // As per original logic in Dektrium's Account::create() if user not found, it doesn't create it here.
        // It just saves the account and the linking happens if user exists.
        // A new user creation is part of the registration flow (e.g. RegistrationController::actionConnect)

        // The $account might have $username and $email set from the client in create() method.
        // If we need to create a user here based on that:
        /*
        if ($account->email === null && $account->username === null) {
            return false; // Cannot create user without email or username
        }

        $user = Yii::createObject([
            'class'    => User::class,
            'scenario' => 'connect', // Important: use 'connect' scenario
            'username' => $account->username,
            'email'    => $account->email,
        ]);

        if ($user->create()) {
            return $user;
        }
        */

        return false; // User not found by email, and not creating here.
    }

    /**
     * Gets the Finder instance.
     * @return Finder
     * @throws InvalidConfigException
     */
    protected static function getFinder()
    {
        if (static::$finder === null) {
            static::$finder = Yii::$container->get(Finder::class);
        }

        return static::$finder;
    }
}
