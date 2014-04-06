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
 * Controller that manages user registration process.
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
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!$this->module->confirmable && in_array($action->id, ['confirm', 'resend'])) {
                throw new NotFoundHttpException('Disabled by administrator');
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Displays the registration page.
     *
     * @return string
     */
    public function actionRegister()
    {
        $model = $this->module->manager->createUser(['scenario' => 'register']);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->register()) {
            return $this->render('success', [
                'model' => $model
            ]);
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
        if ($model->load($_POST) && $model->create()) {
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
     * Confirms user's account.
     *
     * @param $id
     * @param $token
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $token)
    {
        $user = $this->module->manager->findUserByIdAndConfirmationToken($id, $token);
        if ($user === null || !$user->confirm()) {
            return $this->render('invalidToken');
        }

        return $this->render('finish');
    }

    /**
     * Displays page where user can request new confirmation token.
     *
     * @return string
     */
    public function actionResend()
    {
        $model = $this->module->manager->createResendForm();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->getUser()->resend();

            return $this->render('success', [
                'model' => $model
            ]);
        }

        return $this->render('resend', [
            'model' => $model
        ]);
    }
}
