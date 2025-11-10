<?php


/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlexeiKaDev\Yii2User\controllers;

use AlexeiKaDev\Yii2User\Finder;
use AlexeiKaDev\Yii2User\models\RecoveryForm;
use AlexeiKaDev\Yii2User\models\Token;
use AlexeiKaDev\Yii2User\models\enums\TokenType;
use AlexeiKaDev\Yii2User\Module;
use AlexeiKaDev\Yii2User\traits\AjaxValidationTrait;
use AlexeiKaDev\Yii2User\traits\EventTrait;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException; // Added for Yii::createObject, Yii::t, Yii::$app

/**
 * RecoveryController manages password recovery process.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before requesting password reset.
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_BEFORE_REQUEST = 'beforeRequest';

    /**
     * Event is triggered after requesting password reset.
     * Triggered with \AlexeiKaDev\Yii2User\events\FormEvent.
     */
    public const EVENT_AFTER_REQUEST = 'afterRequest';

    /**
     * Event is triggered before validating recovery token.
     * Triggered with \AlexeiKaDev\Yii2User\events\ResetPasswordEvent. May not have $form property set.
     */
    public const EVENT_BEFORE_TOKEN_VALIDATE = 'beforeTokenValidate';

    /**
     * Event is triggered after validating recovery token.
     * Triggered with \AlexeiKaDev\Yii2User\events\ResetPasswordEvent. May not have $form property set.
     */
    public const EVENT_AFTER_TOKEN_VALIDATE = 'afterTokenValidate';

    /**
     * Event is triggered before resetting password.
     * Triggered with \AlexeiKaDev\Yii2User\events\ResetPasswordEvent.
     */
    public const EVENT_BEFORE_RESET = 'beforeReset';

    /**
     * Event is triggered after resetting password.
     * Triggered with \AlexeiKaDev\Yii2User\events\ResetPasswordEvent.
     */
    public const EVENT_AFTER_RESET = 'afterReset';

    /** @var Finder */
    protected $finder;

    /**
     * @param string           $id
     * @param Module $module // Changed from \yii\base\Module to use imported Module
     * @param Finder           $finder
     * @param array            $config
     */
    public function __construct($id, $module, $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['request', 'reset'], 'roles' => ['?']],
                ],
            ],
        ];
    }

    /**
     * Shows page where user can request password recovery.
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRequest()
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class' => RecoveryForm::class,
            'scenario' => RecoveryForm::SCENARIO_REQUEST,
        ]);
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_REQUEST, $event);

        if ($model->load(Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            $this->trigger(self::EVENT_AFTER_REQUEST, $event);

            return $this->render('/message', [
                'title' => Yii::t('user', 'Recovery message sent'),
                'module' => $this->module,
            ]);
        }

        return $this->render('request', [
            'model' => $model,
        ]);
    }

    /**
     * Displays page where user can reset password.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($id, $code)
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var Token|null $token */
        $token = $this->finder->findToken([
            'user_id' => $id,
            'code' => $code,
            'type' => TokenType::RECOVERY
        ])->one();

        // Yii::warning is missing from use statements, assuming it's available globally or via Yii class.
        if ($token === null) {
            Yii::warning("Invalid recovery token received: id=$id, code=$code", __METHOD__);
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.')
            );

            return $this->render('/message', [
                'title' => Yii::t('user', 'Invalid or expired link'),
                'module' => $this->module,
            ]);
        }

        $event = $this->getResetPasswordEvent($token);

        $this->trigger(self::EVENT_BEFORE_TOKEN_VALIDATE, $event);

        if ($token->isExpired || $token->user === null) {
            $this->trigger(self::EVENT_AFTER_TOKEN_VALIDATE, $event);
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.')
            );

            return $this->render('/message', [
                'title' => Yii::t('user', 'Invalid or expired link'),
                'module' => $this->module,
            ]);
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class' => RecoveryForm::class,
            'scenario' => RecoveryForm::SCENARIO_RESET,
        ]);
        $event->setForm($model);

        $this->performAjaxValidation($model);
        $this->trigger(self::EVENT_BEFORE_RESET, $event);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            $this->trigger(self::EVENT_AFTER_RESET, $event);

            return $this->render('/message', [
                'title' => Yii::t('user', 'Password has been changed'),
                'module' => $this->module,
            ]);
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}
