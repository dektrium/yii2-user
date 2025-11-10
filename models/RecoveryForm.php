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

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\services\PasswordRecoveryService;
use Yii;
use yii\base\Model;

/**
 * Model for collecting data on password recovery.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryForm extends Model
{
    public const SCENARIO_REQUEST = 'request';

    public const SCENARIO_RESET = 'reset';

    /**
     * @var string|null
     */
    public $email = null;

    /**
     * @var string|null
     */
    public $password = null;

    /** @var Finder The user finder instance. */
    protected $finder;

    /** @var PasswordRecoveryService The password recovery service instance. */
    protected $passwordRecoveryService;

    /**
     * @param Finder $finder The user finder instance.
     * @param PasswordRecoveryService $passwordRecoveryService The password recovery service instance.
     * @param array  $config Name-value pairs that will be used to initialize the object properties.
     */
    public function __construct(
        Finder $finder,
        PasswordRecoveryService $passwordRecoveryService,
        array $config = []
    ) {
        $this->finder = $finder;
        $this->passwordRecoveryService = $passwordRecoveryService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_REQUEST => ['email'],
            self::SCENARIO_RESET => ['password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'passwordRequired' => ['password', 'required'],
            'passwordLength' => ['password', 'string', 'max' => 72, 'min' => 6],
        ];
    }

    /**
     * Sends recovery message.
     *
     * @return bool
     */
    public function sendRecoveryMessage(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->finder->findUserByEmail((string)$this->email);

        if ($user instanceof User) {
            // Delegate to PasswordRecoveryService
            // The service handles token creation, saving, and sending the message.
            // It also sets flash messages.
            return $this->passwordRecoveryService->request($user);
        } else {
            // User not found, set a generic flash message like the service would for other errors.
            // Or, let the controller handle this if user not found is a specific case for the form.
            // For now, mimic the original behavior of setting a generic success/info message.
             Yii::$app->session->setFlash(
                'info',
                Yii::t('user', 'If an account matching your email exists, then an email has been sent with instructions for resetting your password.')
            );
            // Return true because we don't want to leak whether an email exists or not (security through obscurity)
            return true;
        }
    }

    /**
     * Resets user's password.
     *
     * @param Token $token
     *
     * @return bool
     */
    public function resetPassword(Token $token): bool
    {
        if (!$this->validate() || $token->user === null) {
            return false;
        }

        // Delegate to PasswordRecoveryService
        return $this->passwordRecoveryService->reset($token->user, $token->code, (string)$this->password);
    }

    /**
     * @inheritdoc
     */
    public function formName(): string
    {
        return 'recovery-form';
    }
}
