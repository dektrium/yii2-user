<?php namespace dektrium\user\models;

use yii\behaviors\AutoTimestamp;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\Security;
use yii\web\IdentityInterface;

/**
 * User ActiveRecord model.
 *
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $password_hash
 * @property string  $auth_key
 * @property integer $create_time
 * @property integer $update_time
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * The EVENT_BEFORE_REGISTER event occurs before saving the user in the registration process.
     */
    const EVENT_BEFORE_REGISTER = 'before_registration';

    /**
     * The EVENT_AFTER_REGISTER event occurs after saving the user in the registration process.
     */
    const EVENT_AFTER_REGISTER = 'after_registration';

    /**
     * @var string Plain password. Used for model validation.
     */
    public $password;

    /**
     * @var bool Whether to remember the user.
     */
    public $rememberMe = false;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'register' => ['username', 'email', 'password'],
            'login'    => ['email', 'password']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username, email, password', 'required', 'on' => ['register']],
            ['email, password', 'required', 'on' => ['login']],
            ['email', 'email'],
            ['username, email', 'unique', 'on' => ['register']],
            ['username', 'string', 'min' => 3, 'max' => 25],
            ['email', 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
            ['rememberMe', 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{user}}';
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'user-' . Inflector::camel2id($this->scenario) . '-form';
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
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isAttributeSafe('password') && !empty($this->password)) {
                $this->setAttribute('password_hash', Security::generatePasswordHash($this->password));
            }
            if ($this->isNewRecord) {
                $this->setAttribute('auth_key', Security::generateRandomKey());
                $this->setAttribute('create_time', time());
            }
            $this->setAttribute('update_time', time());
            return true;
        } else {
            return false;
        }
    }

    /**
     * Registers new user.
     *
     * @return bool
     */
    public function register()
    {
        $this->trigger(self::EVENT_BEFORE_REGISTER);
        if ($this->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Account has been created.');
            $this->trigger(self::EVENT_AFTER_REGISTER);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Logs in a user using the provided email and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            /** @var User $user */
            $user = self::find(array('email' => $this->getAttribute('email')));
            if ($user === null || !Security::validatePassword($this->password, $user->getAttribute('password_hash'))) {
                $this->addError('password', 'Incorrect email or password.');
                return false;
            } else {
                \Yii::$app->getUser()->login($user, $this->rememberMe ? \Yii::$app->getModule('user')->params['rememberFor'] : 0);
                \Yii::$app->getSession()->set('user.id', $user->getAttribute('id'));
                \Yii::$app->getSession()->set('user.username', $user->getAttribute('username'));
                \Yii::$app->getSession()->set('user.email', $user->getAttribute('email'));
                return true;
            }
        }

        return false;
    }
}