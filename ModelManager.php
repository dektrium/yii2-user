<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace dektrium\user;

use yii\base\Component;

/**
 * ModelManager is used in order to create models and find users.
 *
 * @method models\User                createUser
 * @method models\Token               createToken
 * @method models\Profile             createProfile
 * @method models\Account             createAccount
 * @method models\UserSearch          createUserSearch
 * @method models\RegistrationForm    createRegistrationForm
 * @method models\ResendForm          createResendForm
 * @method models\LoginForm           createLoginForm
 * @method models\RecoveryForm        createRecoveryForm
 * @method models\RecoveryRequestForm createRecoveryRequestForm
 * @method models\SettingsForm        createSettingsForm
 * @method \yii\db\ActiveQuery        createUserQuery
 * @method \yii\db\ActiveQuery        createTokenQuery
 * @method \yii\db\ActiveQuery        createProfileQuery
 * @method \yii\db\ActiveQuery        createAccountQuery
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ModelManager extends Component
{
    /** @var string */
    public $userClass = 'dektrium\user\models\User';

    /** @var string */
    public $tokenClass = 'dektrium\user\models\Token';

    /** @var string */
    public $profileClass = 'dektrium\user\models\Profile';

    /** @var string */
    public $accountClass = 'dektrium\user\models\Account';

    /** @var string */
    public $userSearchClass = 'dektrium\user\models\UserSearch';

    /** @var string */
    public $registrationFormClass = 'dektrium\user\models\RegistrationForm';

    /** @var string */
    public $resendFormClass = 'dektrium\user\models\ResendForm';

    /** @var string */
    public $loginFormClass = 'dektrium\user\models\LoginForm';

    /** @var string */
    public $recoveryFormClass = 'dektrium\user\models\RecoveryForm';

    /** @var string */
    public $recoveryRequestFormClass = 'dektrium\user\models\RecoveryRequestForm';

    /** @var string */
    public $settingsFormClass = 'dektrium\user\models\SettingsForm';

    /**
     * Finds a user by the given id.
     *
     * @param  integer     $id User id to be used on search.
     * @return models\User
     */
    public function findUserById($id)
    {
        return $this->findUser(['id' => $id])->one();
    }

    /**
     * Finds a user by the given username.
     *
     * @param  string      $username Username to be used on search.
     * @return models\User
     */
    public function findUserByUsername($username)
    {
        return $this->findUser(['username' => $username])->one();
    }

    /**
     * Finds a user by the given email.
     *
     * @param  string      $email Email to be used on search.
     * @return models\User
     */
    public function findUserByEmail($email)
    {
        return $this->findUser(['email' => $email])->one();
    }

    /**
     * Finds a user by the given username or email.
     *
     * @param  string      $usernameOrEmail Username or email to be used on search.
     * @return models\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * Finds a user by the given condition.
     *
     * @param  mixed               $condition Condition to be used on search.
     * @return \yii\db\ActiveQuery
     */
    public function findUser($condition)
    {
        return $this->createUserQuery()->where($condition);
    }

    /**
     * Finds a token by user id and code.
     *
     * @param  integer $userId
     * @param  string  $code
     * @param  integer $type
     * @return models\Token
     */
    public function findToken($userId, $code, $type)
    {
        return $this->createTokenQuery()->where(['user_id' => $userId, 'code' => $code, 'type' => $type])->one();
    }

    /**
     * Finds a profile by user id.
     *
     * @param integer $id
     *
     * @return null|models\Profile
     */
    public function findProfileById($id)
    {
        return $this->findProfile(['user_id' => $id])->one();
    }

    /**
     * Finds a profile
     *
     * @param  mixed $condition
     *
     * @return \yii\db\ActiveQuery
     */
    public function findProfile($condition)
    {
        return $this->createProfileQuery()->where($condition);
    }

    /**
     * Finds an account by id.
     *
     * @param integer $id
     *
     * @return models\Account|null
     */
    public function findAccountById($id)
    {
        return $this->createAccountQuery()->where(['id' => $id])->one();
    }

    /**
     * Finds an account by client id and provider name.
     *
     * @param string $provider
     * @param string $clientId
     *
     * @return models\Account|null
     */
    public function findAccount($provider, $clientId)
    {
        return $this->createAccountQuery()->where([
            'provider'  => $provider,
            'client_id' => $clientId
        ])->one();
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed|object
     */
    public function __call($name, $params)
    {
        $property = (false !== ($query = strpos($name, 'Query'))) ? mb_substr($name, 6, -5) : mb_substr($name, 6);
        $property = lcfirst($property) . 'Class';
        if ($query) {
            return forward_static_call([$this->$property, 'find']);
        }
        if (isset($this->$property)) {
            $config = [];
            if (isset($params[0]) && is_array($params[0])) {
                $config = $params[0];
            }
            $config['class'] = $this->$property;
            return \Yii::createObject($config);
        }

        return parent::__call($name, $params);
    }
}