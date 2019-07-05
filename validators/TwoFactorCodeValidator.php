<?php

namespace dektrium\user\validators;

use Yii;
use RobThree\Auth\TwoFactorAuth;
use yii\di\Instance;
use yii\validators\Validator;

class TwoFactorCodeValidator extends Validator
{
    /** @var string */
    public $secret;

    /** @var string */
    public $secretAttribute;

    /** @var array */
    public $recoveryCodes = [];

    /** @var TwoFactorAuth */
    protected $tfa;

    public function init()
    {
        parent::init();

        if (empty($this->secret) && empty($this->secretAttribute)) {
            throw new \InvalidArgumentException('The $secret or $secretAttributes should be set');
        }

        if ($this->message === null) {
            $this->message = Yii::t('user', '{attribute} is invalid.');
        }

        $this->tfa = Instance::ensure(TwoFactorAuth::class);
    }

    public function validateAttribute($model, $attribute)
    {
        $secret = $this->getSecret($model);
        $value = $model->$attribute;
        if (false === $this->tfa->verifyCode($secret, $value)) {
            $recoveryCodes = $this->recoveryCodes;
            if (is_callable($recoveryCodes)) {
                $recoveryCodes = ($recoveryCodes)($this, $model, $attribute);
            }

            if (empty($recoveryCodes)) {
                $recoveryCodes = [];
            }

            if (empty($recoveryCodes[$value])) {
                $this->addError($model, $attribute, $this->message);
            }
        }
    }

    protected function getSecret($model)
    {
        return isset($this->secret) ? $this->secret : $model->{$this->secretAttribute};
    }
}
