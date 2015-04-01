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
use dektrium\user\Module;
use Yii;
use yii\base\ExitException;
use yii\base\Model;
use yii\base\Module as Module2;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * AdminController allows you to administrate users.
 *
 * @property Module $module
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class AdminController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param Module2 $module
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
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->getIsAdmin();
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
        Url::remember('', 'actions-redirect');
        $searchModel  = Yii::createObject(UserSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->get());

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
        $user = Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);

        $this->performAjaxValidation($user);

        if ($user->load(Yii::$app->request->post()) && $user->create()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));
            return $this->redirect(['update', 'id' => $user->id]);
        }

        return $this->render('create', [
            'user' => $user
        ]);
    }

    /**
     * Updates an existing User model.
     * @param  integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $user->scenario = 'update';

        $this->performAjaxValidation($user);

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Account details have been updated'));
            return $this->refresh();
        }

        return $this->render('_account', [
            'user'    => $user,
        ]);
    }
    
    /**
     * Updates an existing profile.
     * @param  integer $id
     * @return mixed
     */
    public function actionUpdateProfile($id)
    {
        Url::remember('', 'actions-redirect');
        $user    = $this->findModel($id);
        $profile = $user->profile;
        
        $this->performAjaxValidation($profile);
        
        if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Profile details have been updated'));
            return $this->refresh();
        }
        
        return $this->render('_profile', [
            'user'    => $user,
            'profile' => $profile,
        ]);
    }
    
    /**
     * Shows information about user.
     * @param  integer $id
     * @return string
     */
    public function actionInfo($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        
        return $this->render('_info', [
            'user' => $user,
        ]);
    }

    /**
     * If "dektrium/yii2-rbac" extension is installed, this page displays form
     * where user can assign multiple auth items to user.
     * @param  integer $id
     * @return string
     */
    public function actionAssignments($id)
    {
        if (!isset(Yii::$app->extensions['dektrium/yii2-rbac'])) {
            throw new NotFoundHttpException;
        }
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        
        return $this->render('_assignments', [
            'user' => $user,
        ]);
    }
    
    /**
     * Confirms the User.
     * @param integer $id
     * @return Response
     */
    public function actionConfirm($id)
    {
        $this->findModel($id)->confirm();
        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been confirmed'));
        
        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param  integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not remove your own account'));
        } else {
            $this->findModel($id)->delete();
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been deleted'));
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Blocks the user.
     * @param  integer $id
     * @return Response
     */
    public function actionBlock($id)
    {
        if ($id == Yii::$app->user->getId()) {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not block your own account'));
        } else {
            $user = $this->findModel($id);
            if ($user->getIsBlocked()) {
                $user->unblock();
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been unblocked'));
            } else {
                $user->block();
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been blocked'));
            }
        }

        return $this->redirect(Url::previous('actions-redirect'));
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
     * @param array|Model $model
     * @throws ExitException
     */
    protected function performAjaxValidation($model)
    {
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                echo json_encode(ActiveForm::validate($model));
                Yii::$app->end();
            }
        }
    }
}
