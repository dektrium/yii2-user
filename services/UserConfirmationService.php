<?php


namespace AlexeiKaDev\Yii2User\services;

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\Module;
use Yii;
use yii\base\Component;

class UserConfirmationService extends Component
{
    /** @var Finder The finder instance. */
    private $finder;
    /** @var Module The user module instance. */
    private $module;

    public function __construct($finder, $module, $config = [])
    {
        $this->finder = $finder;
        $this->module = $module;
        parent::__construct($config);
    }

    /**
     * Attempts to confirm a user's account with the given code.
     *
     * @param User $user The user to confirm.
     * @param string $code The confirmation code.
     * @return bool True if confirmation was successful, false otherwise.
     */
    public function attempt($user, $code)
    {
        if ($user->getIsConfirmed()) {
            Yii::$app->session->setFlash('info', Yii::t('user', 'Account has already been confirmed'));
            return true; 
        }

        /** @var Token|null $token */
        $token = $this->finder->findToken([
            'user_id' => $user->id,
            'code'    => $code,
            'type'    => TokenType::CONFIRMATION,
        ])->one();

        if ($token === null || $token->getIsExpired()) {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.')
            );
            return false;
        }

        $user->trigger(User::BEFORE_CONFIRM);

        if (!$user->confirm()) { 
            Yii::$app->session->setFlash('danger', Yii::t('user', 'Unable to confirm user. Please try again.'));
            Yii::error('Failed to confirm user ' . $user->id . ': ' . print_r($user->getErrors(), true), __METHOD__);
            return false;
        }
        
        if (!$token->delete()) {
             Yii::error(
                'Failed to delete confirmation token after successful confirmation for user ' . $user->id,
                __METHOD__
            );
        }

        Yii::$app->session->setFlash('success', Yii::t('user', 'Thank you, registration is now complete.'));
        $user->trigger(User::AFTER_CONFIRM);
        
        return true;
    }
} 