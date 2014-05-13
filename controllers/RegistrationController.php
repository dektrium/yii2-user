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
     * After successful registration if confirmable is enabled shows info message otherwise redirects to home page.
     *
     * @return string
     */
    public function actionRegister()
    {
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

        $this->module->confirmable = false;

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
     * Confirms user's account. If confirmation was successful logs the user in and redirects him to homepage. Otherwise
     * renders error message.
     *
     * @param  integer $id
     * @param  string  $token
     * @return string
     * @throws \yii\web\HttpException When token is not found or confirmable is disabled.
     */
    public function actionConfirm($id, $token)
    {
        if (($token = $this->module->manager->findToken($id, $token)) == null || !$this->module->confirmable) {
            throw new NotFoundHttpException;
        }
        try {
            $user = $token->user;
            $user->confirm($token);
            \Yii::$app->user->login($user);
            \Yii::$app->session->setFlash('user.confirmation_finished');
        } catch (\InvalidArgumentException $e) {
            \Yii::$app->session->setFlash('user.invalid_token');
        }

        return $this->render('finish');
    }

    /**
     * Displays page where user can request new confirmation token. If resending was successful, displays message.
     *
     * @return string
     * @throws \yii\web\HttpException When token is not found or confirmable is disabled.
     */
    public function actionResend()
    {
        if (!$this->module->confirmable) {
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
