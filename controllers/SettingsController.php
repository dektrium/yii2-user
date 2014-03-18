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

use dektrium\user\models\Profile;
use yii\web\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\VerbFilter;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'profile';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'reset' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['profile', 'email', 'password', 'reset'],
                        'roles' => ['@']
                    ],
                ]
            ],
        ];
    }

    /**
     * Shows profile settings form.
     *
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        $query = $this->module->factory->createProfileQuery();
        $model = $query->where(['user_id' => \Yii::$app->getUser()->getIdentity()->getId()])->one();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('settings_saved', \Yii::t('user', 'Profile updated successfully'));

            return $this->refresh();
        }

        return $this->render('profile', [
            'model' => $model
        ]);
    }

    /**
     * Shows email settings form.
     *
     * @return string|\yii\web\Response
     */
    public function actionEmail()
    {
        $query = $this->module->factory->createUserQuery();
        $model = $query->where(['id' => \Yii::$app->getUser()->getIdentity()->getId()])->one();
        $model->scenario = 'update_email';

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->updateEmail()) {
            $this->refresh();
        }

        return $this->render('email', [
            'model' => $model
        ]);
    }

    /**
     * Resets email update.
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset()
    {
        if ($this->module->confirmable) {
            $query = $this->module->factory->createUserQuery();
            $model = $query->where(['id' => \Yii::$app->getUser()->getIdentity()->getId()])->one();
            $model->resetEmailUpdate();
            \Yii::$app->getSession()->setFlash('settings_saved', \Yii::t('user', 'Email change has been cancelled'));

            return $this->redirect(['email']);
        }

        throw new NotFoundHttpException;
    }

    /**
     * Shows password settings form.
     *
     * @return string|\yii\web\Response
     */
    public function actionPassword()
    {
        $query = $this->module->factory->createUserQuery();
        $model = $query->where(['id' => \Yii::$app->getUser()->getIdentity()->getId()])->one();
        $model->scenario = 'update_password';

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->updatePassword()) {
            \Yii::$app->getSession()->setFlash('settings_saved', \Yii::t('user', 'Password updated successfully'));
            $this->refresh();
        }

        return $this->render('password', [
            'model' => $model
        ]);
    }
}
