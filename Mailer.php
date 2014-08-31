<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user;

use dektrium\user\models\Token;
use dektrium\user\models\User;
use yii\base\Component;

/**
 * Mailer.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Mailer extends Component
{
    /**
     * @var string
     */
    public $viewPath = '@dektrium/user/views/mail';

    /**
     * @var string|array
     */
    public $sender = 'no-reply@example.com';

    /**
     * Sends an email to a user with credentials and confirmation link.
     *
     * @param  User  $user
     * @param  Token $token
     * @return bool
     */
    public function sendWelcomeMessage(User $user, Token $token = null)
    {
        return $this->sendMessage($user->email,
            \Yii::t('user', 'Welcome to {0}', \Yii::$app->name),
            'welcome',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * Sends an email to a user with confirmation link.
     *
     * @param  User  $user
     * @param  Token $token
     * @return bool
     */
    public function sendConfirmationMessage(User $user, Token $token)
    {
        return $this->sendMessage($user->email,
            \Yii::t('user', 'Confirm your account on {0}', \Yii::$app->name),
            'confirmation',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * Sends an email to a user with reconfirmation link.
     *
     * @param  User  $user
     * @param  Token $token
     * @return bool
     */
    public function sendReconfirmationMessage(User $user, Token $token)
    {
        return $this->sendMessage($user->unconfirmed_email,
            \Yii::t('user', 'Confirm your email change on {0}', \Yii::$app->name),
            'reconfirmation',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * Sends an email to a user with recovery link.
     *
     * @param  User  $user
     * @param  Token $token
     * @return bool
     */
    public function sendRecoveryMessage(User $user, Token $token)
    {
        return $this->sendMessage($user->email,
            \Yii::t('user', 'Complete your password reset on {0}', \Yii::$app->name),
            'recovery',
            ['user' => $user, 'token' => $token]
        );
    }

    /**
     * @param  string $to
     * @param  string $subject
     * @param  string $view
     * @param  array  $params
     * @return bool
     */
    protected function sendMessage($to, $subject, $view, $params = [])
    {
        $mailer = \Yii::$app->mailer;
        $mailer->viewPath = $this->viewPath;

        return $mailer->compose($view, $params)
            ->setTo($to)
            ->setFrom($this->sender)
            ->setSubject($subject)
            ->send();
    }
}