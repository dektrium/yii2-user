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

use yii\web\Controller;
use yii\filters\AccessControl;
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
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['register', 'connect'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm', 'resend'],
                        'roles' => ['?', '@']
                    ],
                ]
            ],
        ];
    }

    /**
     * Displays the registration page.
     * After successful registration if enableConfirmation is enabled shows info message otherwise redirects to home page.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionRegister()
    {
        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException;
        }

        $model = $this->module->manager->createRegistrationForm();

        if ($model->load(\Yii::$app->request->post()) && $model->register()) {
            return $this->render('finish');
        }

        return $this->render('register', [
            'model' => $model
        ]);
    }

    public function actionConnect($account_id)
    {
        $account = $this->module->manager->findAccountById($account_id);

        if ($account === null || $account->getIsConnected()) {
            throw new NotFoundHttpException('Something went wrong');
        }

        $this->module->enableConfirmation = false;

        $model = $this->module->manager->createUser(['scenario' => 'connect']);
        if ($model->load(\Yii::$app->request->post()) && $model->create()) {
            $account->user_id = $model->id;
            $account->save(false);
            \Yii::$app->user->login($model, $this->module->rememberFor);
            $this->goBack();
        }

        return $this->render('connect', [
            'model'   => $model,
            'account' => $account
        ]);
    }

    /**
     * Confirms user's account. If confirmation was successful logs the user and shows success message. Otherwise
     * shows error message.
     *
     * @param  integer $id
     * @param  string  $code
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->module->manager->findUserById($id);

        if ($user === null || $this->module->enableConfirmation == false) {
            throw new NotFoundHttpException;
        }

        if ($user->attemptConfirmation($code)) {
            \Yii::$app->user->login($user);
            \Yii::$app->session->setFlash('user.confirmation_finished');
        } else {
            \Yii::$app->session->setFlash('user.invalid_token');
        }

        return $this->render('finish');
    }

    /**
     * Displays page where user can request new confirmation token. If resending was successful, displays message.
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionResend()
    {
        if (!$this->module->enableConfirmation) {
            throw new NotFoundHttpException;
        }

        $model = $this->module->manager->createResendForm();

        if ($model->load(\Yii::$app->request->post()) && $model->resend()) {
            return $this->render('finish');
        }

        return $this->render('resend', [
            'model' => $model
        ]);
    }
}
