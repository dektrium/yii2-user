<?php

declare(strict_types=1);

namespace AlexeiKaDev\Yii2User\services;

use AlexeiKaDev\Yii2User\events\UserEvent;
use AlexeiKaDev\Yii2User\helpers\Password;
// use AlexeiKaDev\Yii2User\Mailer; // Mailer is now used by UserCreationService
use AlexeiKaDev\Yii2User\models\Profile;
use AlexeiKaDev\Yii2User\models\RegistrationForm;
use AlexeiKaDev\Yii2User\models\User;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\services\UserCreationService; // Corrected namespace for UserCreationService
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception as DbException;

/**
 * Сервис, отвечающий за логику регистрации нового пользователя.
 */
class RegistrationService extends Component
{
    public const EVENT_BEFORE_REGISTER = 'beforeRegister';

    public const EVENT_AFTER_REGISTER = 'afterRegister';

    private Module $module;

    private UserCreationService $userCreationService;

    public function __construct(
        Module $module,
        UserCreationService $userCreationService,
        array $config = []
    ) {
        $this->module = $module;
        $this->userCreationService = $userCreationService;
        parent::__construct($config);
    }

    /**
     * Выполняет процесс регистрации нового пользователя.
     *
     * @param RegistrationForm $form Форма с данными для регистрации.
     * @return User|null Зарегистрированный пользователь или null в случае ошибки.
     * @throws \yii\base\InvalidConfigException
     */
    public function register(RegistrationForm $form): ?User
    {
        /** @var User $user */
        $user = Yii::createObject(User::class);
        $user->setScenario('register');
        $user->setAttributes($form->attributes, false);

        $user->password = ($form->password === null && $this->module->enableGeneratingPassword)
            ? Password::generate(8)
            : $form->password;

        if ($this->module->enableConfirmation) {
            $user->confirmed_at = null;
        } else {
            $user->confirmed_at = time();
        }

        $userEvent = Yii::createObject(['class' => UserEvent::class, 'user' => $user]);
        $this->trigger(self::EVENT_BEFORE_REGISTER, $userEvent);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->userCreationService->create($user)) {
                $form->addErrors($user->getErrors());
                $transaction->rollBack();
                return null;
            }

            /** @var Profile $profile */
            $profile = Yii::createObject(Profile::class);
            if (!$profile->link('user', $user)) { 
                Yii::error(
                    'Failed to link profile for user ' . $user->id . ': ' . print_r($profile->getErrors(), true),
                    __METHOD__
                );
                $transaction->rollBack();
                $form->addError('username', 'Произошла ошибка при создании профиля.');
                return null;
            }
            
            $transaction->commit();

            $userEvent = Yii::createObject(['class' => UserEvent::class, 'user' => $user]);
            $this->trigger(self::EVENT_AFTER_REGISTER, $userEvent);

            return $user;

        } catch (DbException | Throwable $e) {
            $transaction->rollBack();
            Yii::error('Критическая ошибка регистрации пользователя: ' . $e->getMessage() . '\n' . $e->getTraceAsString(), __METHOD__);
            $form->addError('username', 'Произошла критическая ошибка при регистрации. Пожалуйста, попробуйте позже.');
            return null;
        }
    }
}
