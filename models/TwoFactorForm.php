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
use dektrium\user\traits\ModuleTrait;
use dektrium\user\validators\TwoFactorCodeValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\StaleObjectException;

/**
 * TwoFactorForm confirm TFA code for login.
 *
 * @property string $secret
 */
class TwoFactorForm extends Model
{
    use ModuleTrait;

    /** @var string */
    public $code;

    /** @var User */
    protected $user;

    /** @var string */
    protected $_secret;

    /** @var Token[] */
    protected $_recoveryCodes;

    /** @var Finder */
    protected $finder;

    /** @var LoginForm */
    protected $loginForm;

    /**
     * @param Finder $finder
     * @param array $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('user', 'Code'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        $rules = [
            'code' => ['code', 'trim'],
            'codeRequire' => ['code', 'required'],
            'codeVerify' => [
                'code',
                TwoFactorCodeValidator::class,
                'secretAttribute' => 'secret',
                'recoveryCodes' => [$this, 'getRecoveryCodes']
            ],
        ];

        return $rules;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->_secret;
    }

    /**
     * @param $login
     * @return $this
     */
    public function setUserByLogin($login)
    {
        $this->user = $this->finder->findUserByUsernameOrEmail(trim($login));

        $this->_secret = $this->user->tfa_key;

        return $this;
    }

    /**
     * @return Token[]
     */
    public function getRecoveryCodes()
    {
        if (empty($this->_recoveryCodes)) {
            $this->_recoveryCodes = $this->finder->findToken([
                'user_id' => $this->user->id,
                'type' => Token::TYPE_TFA_RECOVERY,
            ])->indexBy(['code'])->all();
        }

        return $this->_recoveryCodes;
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function deleteUserRecoveryCode()
    {
        if (isset($this->getRecoveryCodes()[$this->code])) {
            return (bool)$this->getRecoveryCodes()[$this->code]->delete();
        }

        return false;
    }

    /**
     * @return Finder
     * @throws InvalidConfigException
     */
    protected function getFinder()
    {
        return \Yii::$container->get(Finder::className());
    }
}
