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
use dektrium\user\Mailer;
use dektrium\user\traits\ModuleTrait;
use dektrium\user\validators\TwoFactorCodeValidator;
use RobThree\Auth\TwoFactorAuth;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\di\Instance;

/**
 * TwoFactorEditForm gets user's two factor authentication and changes it.
 *
 * @property bool $isEnabled
 *
 * @property User $user
 * @property Finder $finder
 */
class TwoFactorEditForm extends Model
{
    use ModuleTrait;

    /** @var boolean */
    public $disable = false;

    /** @var string */
    public $secret;

    /** @var string */
    public $code;

    /** @var bool Count bits for creating secret code for TFA */
    public $tfaBits = 160;

    /** @var int */
    public $sizeQrCode = 200;

    /** @var bool|string Issuer name of code in application */
    public $tfaIssuer = false;

    /** @var int */
    public $countRecoveryCodes = 8;

    /** @var int */
    public $lengthRecoveryCode = 10;

    /** @var Mailer */
    protected $mailer;

    /** @var TwoFactorAuth */
    protected $tfa;

    /** @var User */
    private $_user;

    /** @return User */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = Yii::$app->user->identity;
        }

        return $this->_user;
    }

    /** @inheritdoc */
    public function __construct(Mailer $mailer, TwoFactorAuth $tfa, $config = [])
    {
        $this->mailer = $mailer;
        $this->tfa = $tfa;

        if ($this->user->hasTFA) {
            $secret = $this->user->tfa_key;
        } else {
            $secret = $this->tfa->createSecret(
                $this->tfaBits
            );
        }
        $this->setAttributes([
            'secret' => $secret,
        ], false);
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            'disable' => ['disable', 'boolean'],
            'secret' => ['secret', 'string'],
            'secretRequired' => ['secret', 'required'],
            'code' => ['code', 'string'],
            'codeTrim' => ['code', 'trim'],
            'codeRequired' => ['code', 'required'],
            'codeVerify' => ['code', TwoFactorCodeValidator::class, 'secretAttribute' => 'secret']
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'secret' => Yii::t('user', 'Secret'),
            'code' => Yii::t('user', 'Code'),
        ];
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return (bool)$this->user->hasTFA;
    }

    public function getQrCodeUrl()
    {
        $qrCode = $this->tfa->getQRText(
            Yii::t('user', $this->getTfaIssuer(), [
                'username' => $this->user->username,
                'email' => $this->user->email,
            ]),
            $this->secret
        );
        $result = $this->tfa->getQrCodeProvider()->getUrl(
            $qrCode, $this->sizeQrCode
        );

        return $result;
    }

    public function getRecoveryCodes()
    {
        return $this->finder->findToken([
            'user_id' => $this->user->id,
            'type' => Token::TYPE_TFA_RECOVERY,
        ])->all();
    }

    public function save()
    {
        if ($this->disable) {
            return $this->deleteTfaKey();
        }

        if (false === $this->validate()) {
            return false;
        }

        return $this->transaction(function () {
            return $this->initTfaKey() && $this->initRecoveryCodes();
        });
    }

    public function regenerateRecoveryCods()
    {
        return $this->transaction(function () {
            $this->deleteRecoveryCodes();
            return $this->initRecoveryCodes();
        });
    }

    protected function initTfaKey()
    {
        return $this->user->updateAttributes([
            'tfa_key' => $this->secret
        ]);
    }

    protected function deleteTfaKey()
    {
        $this->user->tfa_key = null;

        $this->deleteRecoveryCodes();

        return $this->user->save();
    }

    protected function initRecoveryCodes()
    {
        $indexRecoveryCode = $this->countRecoveryCodes;

        if ($indexRecoveryCode <= 0) {
            return true;
        }

        $recoveryCodes = [];
        while ($indexRecoveryCode > 0) {
            $indexRecoveryCode--;
            $recoveryCode = \Yii::createObject([
                'class' => Token::className(),
                'user_id' => $this->user->id,
                'type' => Token::TYPE_TFA_RECOVERY,
                'length' => $this->lengthRecoveryCode,
            ]);

            if (!$recoveryCode->save(false)) {
                return false;
            }

            $recoveryCodes[] = $recoveryCode;
        }

        if (!$this->mailer->sendRecoveryCodesMessage($this->user, $recoveryCodes)) {
            return false;
        }

        return true;
    }

    protected function deleteRecoveryCodes()
    {
        /** @var Token $tokenClass */
        $tokenClass = Instance::ensure(Token::className());

        return $this->getModule()->getDb()->createCommand()->delete(
            $tokenClass::tableName(),
            [
                'user_id' => $this->user->id,
                'type' => Token::TYPE_TFA_RECOVERY,
            ]
        )->execute();
    }

    public function getTfaIssuer()
    {
        return $this->tfaIssuer ? $this->tfaIssuer : \Yii::$app->name . ' ({email})';
    }

    protected function transaction(callable $callback)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $result = $callback();

            if ($result) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);

            $result = false;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e);

            $result = false;
        }

        return $result;
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
