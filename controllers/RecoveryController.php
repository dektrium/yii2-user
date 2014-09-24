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

use dektrium\user\models\Token;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\base\InvalidParamException;
use yii\web\NotFoundHttpException;

/**
 * RecoveryController manages password recovery process.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends Controller
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
                        'allow'   => true,
                        'actions' => ['request', 'reset'],
                        'roles'   => ['?']
                    ],
                ]
            ],
        ];
    }

    /**
     * Displays page where user can request new recovery message.
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRequest()
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException;
        }

        $model = $this->module->manager->createRecoveryRequestForm();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->sendRecoveryMessage()) {
            return $this->render('finish');
        }

        return $this->render('request', [
            'model' => $model
        ]);
    }

    /**
     * Displays page where user can reset password.
     *
     * @param  integer $id
     * @param  string  $code
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($id, $code)
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException;
        }

        if (($token = $this->module->manager->findToken($id, $code, Token::TYPE_RECOVERY)) == null) {
            throw new NotFoundHttpException;
        }
        try {
            $model = $this->module->manager->createRecoveryForm([
                'token' => $token
            ]);
        } catch (InvalidParamException $e) {
            \Yii::$app->session->setFlash('user.invalid_token');
            return $this->render('finish');
        }

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->resetPassword()) {
            return $this->render('finish');
        }

        return $this->render('reset', [
            'model' => $model
        ]);
    }
}
