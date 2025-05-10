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

use AlexeiKaDev\Yii2User\helpers\Password;
use AlexeiKaDev\Yii2User\Mailer;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\traits\ModuleTrait;
use Yii;
use yii\base\Model;

/**
 * SettingsForm gets user's username, email and password and changes them.
 *
 * @property User $user
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsForm extends Model
{
    use ModuleTrait;

    public const SCENARIO_PROFILE = 'profile';

    public const SCENARIO_ACCOUNT = 'account';

    public const SCENARIO_DELETE = 'delete';

    /** @var string|null */
    public ?string $email = null;

    /** @var string|null */
    public ?string $username = null;

    /** @var string|null */
    public ?string $new_password = null;

    /** @var string|null */
    public ?string $current_password = null;

    private ?User $_user = null;

    public function getUser(): User
    {
        if ($this->_user === null) {
            $this->_user = Yii::$app->user->identity;
        }

        return $this->_user;
    }

    /**
     * @param Mailer $mailer The mailer instance.
     * @param array $config Name-value pairs that will be used to initialize the object properties.
     */
    public function __construct(
        protected Mailer $mailer,
        array $config = []
    ) {
        parent::__construct($config);
        $user = $this->getUser();

        if ($user) {
            $this->setAttributes([
                'username' => $user->username,
                'email' => $user->unconfirmed_email ?: $user->email,
            ], false);
        }
    }

    /** @inheritdoc */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_PROFILE] = ['email', 'username'];
        $scenarios[self::SCENARIO_ACCOUNT] = ['username', 'email', 'new_password', 'current_password'];
        $scenarios[self::SCENARIO_DELETE] = ['current_password'];

        return $scenarios;
    }

    public function rules(): array
    {
        return [
            'usernameTrim' => ['username', 'trim'],
            'usernameRequired' => ['username', 'required', 'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_PROFILE]],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255, 'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_PROFILE]],
            'usernamePattern' => ['username', 'match', 'pattern' => User::$usernameRegexp, 'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_PROFILE]],

            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required', 'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_PROFILE]],
            'emailPattern' => ['email', 'email', 'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_PROFILE]],

            'emailUsernameUnique' => [
                ['email', 'username'],
                'unique',
                'targetClass' => $this->module->modelMap['User'],
                'when' => fn ($model, $attribute) => $this->getUser()->$attribute != $model->$attribute,
                'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_PROFILE]
            ],

            'newPasswordLength' => ['new_password', 'string', 'max' => 72, 'min' => 6, 'on' => self::SCENARIO_ACCOUNT],

            'currentPasswordRequired' => ['current_password', 'required', 'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_DELETE]],
            'currentPasswordValidate' => ['current_password', function (string $attribute) {
                if (!Password::validate((string)$this->$attribute, $this->getUser()->password_hash)) {
                    $this->addError($attribute, Yii::t('user', 'Current password is not valid'));
                }
            }, 'on' => [self::SCENARIO_ACCOUNT, self::SCENARIO_DELETE]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('user', 'Email'),
            'username' => Yii::t('user', 'Username'),
            'new_password' => Yii::t('user', 'New password'),
            'current_password' => Yii::t('user', 'Current password'),
        ];
    }

    public function formName(): string
    {
        return 'settings-form';
    }

    public function save(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->scenario = 'settings';
            $user->username = (string)$this->username;

            if ($this->new_password) {
                $user->password = (string)$this->new_password;
            }

            $emailChanged = false;

            if ($this->email && $this->email !== $user->email) {
                if ($user->unconfirmed_email !== null && $this->email === $user->email) {
                    $user->unconfirmed_email = null;
                } else {
                    match ($this->module->emailChangeStrategy) {
                        Module::STRATEGY_INSECURE => $this->insecureEmailChange($user),
                        Module::STRATEGY_DEFAULT => $this->defaultEmailChange($user),
                        Module::STRATEGY_SECURE => $this->secureEmailChange($user),
                        default => throw new \OutOfBoundsException('Invalid email changing strategy'),
                    };
                }
            } elseif ($this->email === $user->email && $user->unconfirmed_email !== null) {
                $user->unconfirmed_email = null;
            }

            return $user->save();
        }

        return false;
    }

    /**
     * Performs insecure email change.
     * @param User $user The user whose email needs to be changed.
     */
    protected function insecureEmailChange(User $user): void
    {
        $user->email = (string)$this->email;
        Yii::$app->session->setFlash('success', Yii::t('user', 'Your email address has been changed'));
    }

    /**
     * Performs default email change (sends confirmation to new email).
     * @param User $user The user whose email needs to be changed.
     */
    protected function defaultEmailChange(User $user): void
    {
        $user->unconfirmed_email = (string)$this->email;
        /** @var Token $token */
        $token = Yii::createObject([
            'class' => Token::class,
            'user_id' => $user->id,
            'type' => TokenType::CONFIRM_NEW_EMAIL,
        ]);
        $token->save(false);
        $this->mailer->sendReconfirmationMessage($user, $token);
        Yii::$app->session->setFlash(
            'info',
            Yii::t('user', 'A confirmation message has been sent to your new email address')
        );
    }

    /**
     * Performs secure email change (sends confirmation to both old and new emails).
     * @param User $user The user whose email needs to be changed.
     */
    protected function secureEmailChange(User $user): void
    {
        $this->defaultEmailChange($user);
        /** @var Token $token */
        $token = Yii::createObject([
            'class' => Token::class,
            'user_id' => $user->id,
            'type' => TokenType::CONFIRM_OLD_EMAIL,
        ]);
        $token->save(false);
        $this->mailer->sendReconfirmationMessage($user, $token);

        Yii::$app->session->setFlash(
            'info',
            Yii::t(
                'user',
                'We have sent confirmation links to both old and new email addresses. You must click both links to complete your request'
            )
        );
    }
}
