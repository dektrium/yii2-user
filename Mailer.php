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

namespace AlexeiKaDev\Yii2User;

use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\User;
use Yii;
use yii\base\Component;
use yii\mail\BaseMailer;

/**
 * Mailer.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Mailer extends Component
{
    /** @var string Path to mail view files. */
    public string $viewPath = '@AlexeiKaDev/Yii2User/views/mail';

    /** @var string|array|null Sender email address or array ['email' => 'name']. If null, uses a default value. */
    public string|array|null $sender = null;

    /** @var BaseMailer|null The mailer component instance. If null, uses Yii::$app->mailer. */
    public ?BaseMailer $mailerComponent = null;

    /** @var string|null Subject for the welcome message. */
    protected ?string $welcomeSubject = null;

    /** @var string|null Subject for the new password message. */
    protected ?string $newPasswordSubject = null;

    /** @var string|null Subject for the confirmation message. */
    protected ?string $confirmationSubject = null;

    /** @var string|null Subject for the reconfirmation message. */
    protected ?string $reconfirmationSubject = null;

    /** @var string|null Subject for the recovery message. */
    protected ?string $recoverySubject = null;

    /** @var Module The user module instance. */
    protected Module $module;

    public function getWelcomeSubject(): string
    {
        if ($this->welcomeSubject === null) {
            $this->setWelcomeSubject(Yii::t('user', 'Welcome to {0}', Yii::$app->name));
        }

        return $this->welcomeSubject;
    }

    public function setWelcomeSubject(string $welcomeSubject): void
    {
        $this->welcomeSubject = $welcomeSubject;
    }

    public function getNewPasswordSubject(): string
    {
        if ($this->newPasswordSubject === null) {
            $this->setNewPasswordSubject(Yii::t('user', 'Your password on {0} has been changed', Yii::$app->name));
        }

        return $this->newPasswordSubject;
    }

    public function setNewPasswordSubject(string $newPasswordSubject): void
    {
        $this->newPasswordSubject = $newPasswordSubject;
    }

    public function getConfirmationSubject(): string
    {
        if ($this->confirmationSubject === null) {
            $this->setConfirmationSubject(Yii::t('user', 'Confirm account on {0}', Yii::$app->name));
        }

        return $this->confirmationSubject;
    }

    public function setConfirmationSubject(string $confirmationSubject): void
    {
        $this->confirmationSubject = $confirmationSubject;
    }

    public function getReconfirmationSubject(): string
    {
        if ($this->reconfirmationSubject === null) {
            $this->setReconfirmationSubject(Yii::t('user', 'Confirm email change on {0}', Yii::$app->name));
        }

        return $this->reconfirmationSubject;
    }

    public function setReconfirmationSubject(string $reconfirmationSubject): void
    {
        $this->reconfirmationSubject = $reconfirmationSubject;
    }

    public function getRecoverySubject(): string
    {
        if ($this->recoverySubject === null) {
            $this->setRecoverySubject(Yii::t('user', 'Complete password reset on {0}', Yii::$app->name));
        }

        return $this->recoverySubject;
    }

    public function setRecoverySubject(string $recoverySubject): void
    {
        $this->recoverySubject = $recoverySubject;
    }

    public function init(): void
    {
        parent::init();
        $this->module = Yii::$app->getModule('user');

        $this->sender = $this->sender ?? Yii::$app->params['adminEmail'] ?? 'no-reply@example.com';

        $this->mailerComponent = $this->mailerComponent ?? Yii::$app->mailer;
    }

    public function sendWelcomeMessage(User $user, ?Token $token = null, bool $showPassword = false): bool
    {
        return $this->sendMessage(
            $user->email,
            $this->getWelcomeSubject(),
            'welcome',
            ['user' => $user, 'token' => $token, 'module' => $this->module, 'showPassword' => $showPassword]
        );
    }

    public function sendGeneratedPassword(User $user, string $password): bool
    {
        return $this->sendMessage(
            $user->email,
            $this->getNewPasswordSubject(),
            'new_password',
            ['user' => $user, 'password' => $password, 'module' => $this->module]
        );
    }

    public function sendConfirmationMessage(User $user, Token $token): bool
    {
        return $this->sendMessage(
            $user->email,
            $this->getConfirmationSubject(),
            'confirmation',
            ['user' => $user, 'token' => $token]
        );
    }

    public function sendReconfirmationMessage(User $user, Token $token): bool
    {
        $tokenType = ($token->type instanceof TokenType) ? $token->type : TokenType::from((int)$token->type);

        $email = ($tokenType === TokenType::CONFIRM_NEW_EMAIL && !empty($user->unconfirmed_email))
            ? $user->unconfirmed_email
            : $user->email;

        return $this->sendMessage(
            (string)$email,
            $this->getReconfirmationSubject(),
            'reconfirmation',
            ['user' => $user, 'token' => $token]
        );
    }

    public function sendRecoveryMessage(User $user, Token $token): bool
    {
        return $this->sendMessage(
            $user->email,
            $this->getRecoverySubject(),
            'recovery',
            ['user' => $user, 'token' => $token]
        );
    }

    protected function sendMessage(string $to, string $subject, string $view, array $params = []): bool
    {
        $mailer = $this->mailerComponent;

        if (!$mailer instanceof BaseMailer) {
            Yii::error('Mailer component is not configured or invalid.', __METHOD__);

            return false;
        }
        $mailer->viewPath = $this->viewPath;
        $mailer->getView()->theme = Yii::$app->view->theme;

        if ($this->sender === null) {
            $this->sender = Yii::$app->params['adminEmail'] ?? 'no-reply@example.com';
        }

        return $mailer->compose(['html' => $view, 'text' => 'text/' . $view], $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }
}
