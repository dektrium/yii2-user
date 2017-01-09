<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dektrium\user\controllers;

use dektrium\user\service\exceptions\ServiceException;
use dektrium\user\service\RegistrationService;
use dektrium\user\service\UserConfirmation;
use dektrium\user\models\Account;
use dektrium\user\models\RegistrationForm;
use dektrium\user\models\ResendForm;
use dektrium\user\models\User;
use dektrium\user\traits\AjaxValidationTrait;
use dektrium\user\traits\EventTrait;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered after creating RegistrationForm class.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_BEFORE_REGISTER = 'beforeRegister';

    /**
     * Event is triggered after successful registration.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_AFTER_REGISTER = 'afterRegister';

    /**
     * Event is triggered before connecting user to social account.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONNECT = 'beforeConnect';

    /**
     * Event is triggered after connecting user to social account.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_CONNECT = 'afterConnect';

    /**
     * Event is triggered before confirming user.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

    /**
     * Event is triggered before confirming user.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_CONFIRM = 'afterConfirm';

    /**
     * Event is triggered after creating ResendForm class.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_BEFORE_RESEND = 'beforeResend';

    /**
     * Event is triggered after successful resending of confirmation email.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_AFTER_RESEND = 'afterResend';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['register', 'connect'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['confirm', 'resend'], 'roles' => ['?', '@']],
                ],
            ],
        ];
    }

    /**
     * Displays the registration page.
     * After successful registration redirects to login page.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionRegister()
    {
        /** @var RegistrationForm $model */
        $model = \Yii::createObject(RegistrationForm::className(), [$this->createRegistrationService()]);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_REGISTER, $this->getFormEvent($model));
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->getRegistrationService()->register($model);
            $this->trigger(self::EVENT_AFTER_REGISTER, $this->getFormEvent($model));
            return $this->redirect(['/user/security/login']);
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Displays page where user can create new account that will be connected to social account.
     *
     * @param string $code
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionConnect($code)
    {
        /** @var Account $account */
        $account = \Yii::createObject(Account::className());
        $account = $account::find()->byCode($code)->one();

        if ($account === null || $account->getIsConnected()) {
            throw new NotFoundHttpException();
        }

        /** @var User $user */
        $user = \Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'connect',
            'username' => $account->username,
            'email'    => $account->email,
        ]);

        $event = $this->getConnectEvent($account, $user);

        $this->trigger(self::EVENT_BEFORE_CONNECT, $event);

        if ($user->load(\Yii::$app->request->post()) && $user->create()) {
            $account->connect($user);
            $this->trigger(self::EVENT_AFTER_CONNECT, $event);
            \Yii::$app->user->login($user, $this->module->rememberFor);
            return $this->goBack();
        }

        return $this->render('connect', [
            'model'   => $user,
            'account' => $account,
        ]);
    }

    /**
     * Attempts confirmation by code.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code)
    {
        /** @var User $user */
        $user = \Yii::createObject(User::className());
        $user = $user::findOne($id);
        $domain = $this->createConfirmationService();

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $this->getUserEvent($user));
        try {
            $domain->attemptConfirmation($user, $code);
        } catch (ServiceException $e) {
            \Yii::error($e);
            return $this->redirect(['/user/security/login']);
        }
        $this->trigger(self::EVENT_AFTER_CONFIRM, $this->getUserEvent($user));

        return \Yii::$app->user->getIsGuest()
            ? $this->redirect(['/user/security/login'])
            : $this->goHome();
    }

    /**
     * Displays page where user can request new confirmation token. If resending was successful, displays message.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionResend()
    {
        /** @var ResendForm $model */
        $model = \Yii::createObject(ResendForm::className());
        $domain = $this->createConfirmationService();

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_RESEND, $this->getFormEvent($model));
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            try {
                $domain->resendConfirmationMessage($model->getUser());
                $this->trigger(self::EVENT_AFTER_RESEND, $this->getFormEvent($model));
            } catch (ServiceException $e) {
                \Yii::error($e);
            }
            return $this->redirect(['/user/security/login']);
        }

        return $this->render('resend', [
            'model' => $model,
        ]);
    }

    /**
     * @return RegistrationService
     * @throws NotFoundHttpException
     */
    protected function createRegistrationService()
    {
        /** @var RegistrationService $service */
        $service = \Yii::createObject(RegistrationService::className());
        if (!$service->isEnabled) {
            throw new NotFoundHttpException('Page not found');
        }
        return $service;
    }

    /**
     * @return UserConfirmation|object
     * @throws NotFoundHttpException
     */
    protected function createConfirmationService()
    {
        /** @var UserConfirmation $service */
        $service = \Yii::createObject(UserConfirmation::className());
        if (!$service->isEnabled || !$service->isEmailConfirmationEnabled) {
            throw new NotFoundHttpException('Page not found');
        }
        return $service;
    }
}
