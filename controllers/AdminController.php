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

use dektrium\user\models\UserSearch;
use yii\web\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\VerbFilter;

/**
 * AdminController allows you to administrate users.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class AdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'delete-tokens' => ['post'],
                    'block' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'block', 'confirm', 'delete-tokens'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return in_array(\Yii::$app->user->identity->username, $this->module->admins);
                        }
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new UserSearch();
        $dataProvider = $searchModel->search($_GET);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var \dektrium\user\models\User $model */
        $model = $this->module->factory->createUser();
        $model->scenario = 'create';

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('admin_user', \Yii::t('user', 'User has been created'));

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('admin_user', \Yii::t('user', 'User has been updated'));

            return $this->refresh();
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Confirms the User.
     * @param $id
     * @return \yii\web\Response
     */
    public function actionConfirm($id)
    {
        $this->findModel($id)->confirm(false);
        \Yii::$app->getSession()->setFlash('admin_user', \Yii::t('user', 'User has been confirmed'));

        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * Deletes recovery tokens.
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDeleteTokens($id)
    {
        $model = $this->findModel($id);
        $model->recovery_token = null;
        $model->recovery_sent_at = null;
        $model->save(false);
        \Yii::$app->getSession()->setFlash('admin_user', \Yii::t('user', 'All user tokens have been deleted'));

        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->getSession()->setFlash('admin_user', \Yii::t('user', 'User has been deleted'));

        return $this->redirect(['index']);
    }

    /**
     * Blocks the user.
     *
     * @param $id
     * @return \yii\web\Response
     */
    public function actionBlock($id)
    {
        $user = $this->findModel($id);
        if ($user->getIsBlocked()) {
            $user->unblock();
            \Yii::$app->getSession()->setFlash('admin_user', \Yii::t('user', 'User has been unblocked'));
        } else {
            $user->block();
            \Yii::$app->getSession()->setFlash('admin_user', \Yii::t('user', 'User has been blocked'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer                    $id
     * @return \dektrium\user\models\User the loaded model
     * @throws NotFoundHttpException      if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var \dektrium\user\models\User $user */
        $user = $this->module->factory->createUserQuery()->where(['id' => $id])->one();
        if ($id !== null && $user !== null) {
            return $user;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
