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

use dektrium\user\Finder;
use dektrium\user\models\User;
use dektrium\user\models\UserSearch;
use yii\base\Model;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * AdminController allows you to administrate users.
 *
 * @property \dektrium\user\Module $module
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class AdminController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'  => ['post'],
                    'confirm' => ['post'],
                    'block'   => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'block', 'confirm'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->getIsAdmin();
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
        $searchModel  = \Yii::createObject(UserSearch::className());
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
        /** @var User $user */
        $user = \Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);

        $this->performAjaxValidation($user);

        if ($user->load(\Yii::$app->request->post()) && $user->create()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been created'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'user' => $user
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
        $user = $this->findModel($id);
        $user->scenario = 'update';
        $profile = $this->finder->findProfileById($id);
        $r = \Yii::$app->request;

        $this->performAjaxValidation([$user, $profile]);

        if ($user->load($r->post()) && $profile->load($r->post()) && $user->save() && $profile->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been updated'));
            return $this->refresh();
        }

        return $this->render('update', [
            'user'    => $user,
            'profile' => $profile,
            'module'  => $this->module,
        ]);
    }

    /**
     * Confirms the User.
     * @param integer $id
     * @param string  $back
     * @return \yii\web\Response
     */
    public function actionConfirm($id, $back = 'index')
    {
        $this->findModel($id)->confirm();
        \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been confirmed'));
        $url = $back == 'index' ? ['index'] : ['update', 'id' => $id];
        return $this->redirect($url);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($id == \Yii::$app->user->getId()) {
            \Yii::$app->getSession()->setFlash('danger', \Yii::t('user', 'You can not remove your own account'));
        } else {
            $this->findModel($id)->delete();
            \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been deleted'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Blocks the user.
     * @param  integer $id
     * @param  string  $back
     * @return \yii\web\Response
     */
    public function actionBlock($id, $back = 'index')
    {
        if ($id == \Yii::$app->user->getId()) {
            \Yii::$app->getSession()->setFlash('danger', \Yii::t('user', 'You can not block your own account'));
        } else {
            $user = $this->findModel($id);
            if ($user->getIsBlocked()) {
                $user->unblock();
                \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been unblocked'));
            } else {
                $user->block();
                \Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'User has been blocked'));
            }
        }
        $url = $back == 'index' ? ['index'] : ['update', 'id' => $id];
        return $this->redirect($url);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param  integer               $id
     * @return User                  the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = $this->finder->findUserById($id);
        if ($user === null) {
            throw new NotFoundHttpException('The requested page does not exist');
        }
        return $user;
    }

    /**
     * Performs AJAX validation.
     * @param array|Model $models
     * @throws \yii\base\ExitException
     */
    protected function performAjaxValidation($models)
    {
        if (\Yii::$app->request->isAjax) {
            if (is_array($models)) {
                $result = [];
                foreach ($models as $model) {
                    if ($model->load(\Yii::$app->request->post())) {
                        \Yii::$app->response->format = Response::FORMAT_JSON;
                        $result = array_merge($result, ActiveForm::validate($model));
                    }
                }
                echo json_encode($result);
                \Yii::$app->end();
            } else {
                if ($models->load(\Yii::$app->request->post())) {
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    echo json_encode(ActiveForm::validate($models));
                    \Yii::$app->end();
                }
            }
        }
    }
}
