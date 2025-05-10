<?php

declare(strict_types=1);

namespace AlexeiKaDev\Yii2User\services;

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\Mailer;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\Module;
use Yii;
use yii\base\Component;

class PasswordRecoveryService extends Component
{
    /** @var Finder The finder instance. */
    private Finder $finder;
    /** @var Mailer The mailer instance. */
    private Mailer $mailer;
    /** @var Module The user module instance. */
    private Module $module;

    public function __construct(Finder $finder, Mailer $mailer, Module $module, array $config = [])
    {
        $this->finder = $finder;
        $this->mailer = $mailer;
        $this->module = $module;
        parent::__construct($config);
    }

    /**
     * Requests a password reset token for the user.
     *
     * @param User $user
     * @return bool True if the request was processed successfully (email sent or error handled).
     */
    public function request(User $user): bool
    {
        if (!$this->module->enablePasswordRecovery) {
            Yii::$app->session->setFlash('warning', Yii::t('user', 'Password recovery is disabled.'));
            return false;
        }

        if (!$user->getIsConfirmed()) {
             Yii::$app->session->setFlash(
                'warning',
                Yii::t('user', 'You need to confirm your email address first before changing password.')
            );
            return false;
        }
        
        /** @var Token $token */
        $token = Yii::createObject([
            'class'   => Token::class,
            'user_id' => $user->id,
            'type'    => TokenType::RECOVERY,
        ]);

        if (!$token->save()) {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'Unable to send recovery message to the user.'));
            Yii::error('Failed to save recovery token for user ' . $user->id . ': ' . print_r($token->getErrors(), true), __METHOD__);
            return false;
        }
        
        if ($this->mailer->sendRecoveryMessage($user, $token)) {
            Yii::$app->session->setFlash('info', Yii::t('user', 'An email has been sent with instructions for resetting your password.'));
            return true;
        }
        
        Yii::$app->session->setFlash('danger', Yii::t('user', 'Unable to send recovery message to the user.'));
        Yii::error('Failed to send recovery message for user ' . $user->id, __METHOD__);
        return false;
    }

    /**
     * Resets the user's password using a recovery token.
     *
     * @param User $user
     * @param string $code The recovery token code.
     * @param string $newPassword The new plain password.
     * @return bool True if the password was reset successfully.
     */
    public function reset(User $user, string $code, string $newPassword): bool
    {
        /** @var Token|null $token */
        $token = $this->finder->findToken([
            'user_id' => $user->id,
            'code'    => $code,
            'type'    => TokenType::RECOVERY,
        ])->one();

        if ($token === null || $token->getIsExpired() || !$this->module->enablePasswordRecovery) {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'The recovery link is invalid or expired. Please try requesting a new one.')
            );
            return false;
        }

        if ($user->resetPassword($newPassword)) {
            if (!$token->delete()) {
                Yii::error(
                    'Failed to delete recovery token after successful password reset for user ' . $user->id,
                    __METHOD__
                );
                // Non-critical for user flow, but an issue.
            }
            Yii::$app->session->setFlash('success', Yii::t('user', 'Password has been changed.'));
            return true;
        }
        
        Yii::$app->session->setFlash('danger', Yii::t('user', 'Unable to change password. Please try again.'));
        Yii::error('Failed to reset password for user ' . $user->id . ': ' . print_r($user->getErrors(), true), __METHOD__);
        return false;
    }
} 