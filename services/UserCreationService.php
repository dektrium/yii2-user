<?php


namespace AlexeiKaDev\Yii2User\services;

use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\Mailer;
use AlexeiKaDev\Yii2User\Module;
use Yii;
use yii\base\Component;

/**
 * Service responsible for user creation logic.
 */
class UserCreationService extends Component
{
    /** @var Mailer The mailer instance. */
    private $mailer;
    /** @var Module The user module instance. */
    private $module;

    public function __construct($mailer, $module, $config = [])
    {
        $this->mailer = $mailer;
        $this->module = $module;
        parent::__construct($config);
    }

    /**
     * Creates a new user, sends confirmation email if needed.
     * Assumes the User model has been validated for basic fields (username, email, password hash).
     *
     * @param User $user The user model instance, expected to be new and validated.
     * @return bool True if user was created successfully, false otherwise.
     * @throws \RuntimeException If called on an existing user.
     */
    public function create($user)
    {
        if (!$user->getIsNewRecord()) {
            throw new \RuntimeException('Calling UserCreationService::create() on an existing user.');
        }

        $user->trigger(User::BEFORE_CREATE);

        if ($user->save()) {
            $user->trigger(User::AFTER_CREATE);

            if ($this->module->enableConfirmation && !$user->getIsConfirmed()) {
                /** @var Token $token */
                $token = Yii::createObject([
                    'class' => Token::class,
                    'user_id' => $user->id,
                    'type' => TokenType::CONFIRMATION
                ]);
                
                if (!$token->save()) {
                     Yii::error(
                        'Failed to save confirmation token for user ' . $user->id . ': ' . print_r($token->getErrors(), true),
                        __METHOD__
                    );
                } else {
                    $this->mailer->sendConfirmationMessage($user, $token);
                }
            }
            Yii::info('User has been created by UserCreationService. ID: ' . $user->id, __METHOD__);
            return true;
        }

        Yii::error('User storing failed in UserCreationService for username ' . $user->username . ': ' . print_r($user->getErrors(), true), __METHOD__);
        return false;
    }
} 