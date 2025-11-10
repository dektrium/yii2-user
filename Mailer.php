<?php

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
    public $viewPath = '@AlexeiKaDev/Yii2User/views/mail';

    /** @var string|array|null Sender email address or array ['email' => 'name']. If null, uses a default value. */
    public $sender = null;

    /** @var BaseMailer|null The mailer component instance. If null, uses Yii::$app->mailer. */
    public $mailerComponent = null;

    /** @var string|null Subject for the welcome message. */
    protected $welcomeSubject = null;

    /** @var string|null Subject for the new password message. */
    protected $newPasswordSubject = null;

    /** @var string|null Subject for the confirmation message. */
    protected $confirmationSubject = null;

    /** @var string|null Subject for the reconfirmation message. */
    protected $reconfirmationSubject = null;

    /** @var string|null Subject for the recovery message. */
    protected $recoverySubject = null;

    /** @var Module The user module instance. */
    protected $module;

    /**
     * @return string
     */
    public function getWelcomeSubject()
    {
        if ($this->welcomeSubject === null) {
            $this->setWelcomeSubject(Yii::t('user', 'Welcome to {0}', Yii::$app->name));
        }

        return $this->welcomeSubject;
    }

    /**
     * @param string $welcomeSubject
     */
    public function setWelcomeSubject($welcomeSubject)
    {
        $this->welcomeSubject = $welcomeSubject;
    }

    /**
     * @return string
     */
    public function getNewPasswordSubject()
    {
        if ($this->newPasswordSubject === null) {
            $this->setNewPasswordSubject(Yii::t('user', 'Your password on {0} has been changed', Yii::$app->name));
        }

        return $this->newPasswordSubject;
    }

    /**
     * @param string $newPasswordSubject
     */
    public function setNewPasswordSubject($newPasswordSubject)
    {
        $this->newPasswordSubject = $newPasswordSubject;
    }

    /**
     * @return string
     */
    public function getConfirmationSubject()
    {
        if ($this->confirmationSubject === null) {
            $this->setConfirmationSubject(Yii::t('user', 'Confirm account on {0}', Yii::$app->name));
        }

        return $this->confirmationSubject;
    }

    /**
     * @param string $confirmationSubject
     */
    public function setConfirmationSubject($confirmationSubject)
    {
        $this->confirmationSubject = $confirmationSubject;
    }

    /**
     * @return string
     */
    public function getReconfirmationSubject()
    {
        if ($this->reconfirmationSubject === null) {
            $this->setReconfirmationSubject(Yii::t('user', 'Confirm email change on {0}', Yii::$app->name));
        }

        return $this->reconfirmationSubject;
    }

    /**
     * @param string $reconfirmationSubject
     */
    public function setReconfirmationSubject($reconfirmationSubject)
    {
        $this->reconfirmationSubject = $reconfirmationSubject;
    }

    /**
     * @return string
     */
    public function getRecoverySubject()
    {
        if ($this->recoverySubject === null) {
            $this->setRecoverySubject(Yii::t('user', 'Complete password reset on {0}', Yii::$app->name));
        }

        return $this->recoverySubject;
    }

    /**
     * @param string $recoverySubject
     */
    public function setRecoverySubject($recoverySubject)
    {
        $this->recoverySubject = $recoverySubject;
    }

    public function init()
    {
        parent::init();
        $this->module = Yii::$app->getModule('user');

        if ($this->sender === null) {
            $this->sender = isset(Yii::$app->params['adminEmail']) ? Yii::$app->params['adminEmail'] : 'no-reply@example.com';
        }

        if ($this->mailerComponent === null) {
            $this->mailerComponent = Yii::$app->mailer;
        }
    }

    /**
     * @param User $user
     * @param Token|null $token
     * @param bool $showPassword
     * @return bool
     */
    public function sendWelcomeMessage(User $user, $token = null, $showPassword = false)
    {
        return $this->sendMessage(
            $user->email,
            $this->getWelcomeSubject(),
            'welcome',
            ['user' => $user, 'token' => $token, 'module' => $this->module, 'showPassword' => $showPassword]
        );
    }

    /**
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function sendGeneratedPassword(User $user, $password)
    {
        return $this->sendMessage(
            $user->email,
            $this->getNewPasswordSubject(),
            'new_password',
            ['user' => $user, 'password' => $password, 'module' => $this->module]
        );
    }

    /**
     * @param User $user
     * @param Token $token
     * @return bool
     */
    public function sendConfirmationMessage(User $user, Token $token)
    {
        return $this->sendMessage(
            $user->email,
            $this->getConfirmationSubject(),
            'confirmation',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * @param User $user
     * @param Token $token
     * @return bool
     */
    public function sendReconfirmationMessage(User $user, Token $token)
    {
        // Token type is now always stored as integer
        $tokenType = (int)$token->type;

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

    /**
     * @param User $user
     * @param Token $token
     * @return bool
     */
    public function sendRecoveryMessage(User $user, Token $token)
    {
        return $this->sendMessage(
            $user->email,
            $this->getRecoverySubject(),
            'recovery',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $params
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, array $params = [])
    {
        $mailer = $this->mailerComponent;

        if (!$mailer instanceof BaseMailer) {
            Yii::error('Mailer component is not configured or invalid.', __METHOD__);

            return false;
        }
        $mailer->viewPath = $this->viewPath;
        $mailer->getView()->theme = Yii::$app->view->theme;

        if ($this->sender === null) {
            $this->sender = isset(Yii::$app->params['adminEmail']) ? Yii::$app->params['adminEmail'] : 'no-reply@example.com';
        }

        return $mailer->compose(['html' => $view, 'text' => 'text/' . $view], $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }
}
