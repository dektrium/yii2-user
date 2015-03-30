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
use dektrium\user\models\Account;
use dektrium\user\models\LoginForm;
use dektrium\user\Module;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\base\ExitException;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Controller that manages user authentication process.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SecurityController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array  $config
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['login', 'auth'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['login', 'logout'], 'roles' => ['@']],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

    /** @inheritdoc */
    public function actions()
    {
        return [
            'auth' => [
                'class'           => AuthAction::className(),
                'successCallback' => [$this, 'authenticate'],
            ]
        ];
    }

    /**
     * Displays the login page.
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $model = \Yii::createObject(LoginForm::className());

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Logs the user out and then redirects to the homepage.
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        return $this->goHome();
    }

    /**
     * Tries to authenticate user via social network. If user has alredy used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *  
     * @param  ClientInterface $client
     * @return Response
     */
    public function authenticate(ClientInterface $client)
    {
        $account = Account::createFromClient($client);
        $user    = $account->user;
        
        if (null === $user) {
            $this->action->successUrl = Url::to([
                '/user/registration/connect',
                'account_id' => $account->id
            ]);
        } else {
            Yii::$app->user->login($user, $this->module->rememberFor);
        }
    }

    /**
     * Performs ajax validation.
     * @param Model $model
     * @throws ExitException
     */
    protected function performAjaxValidation(Model $model)
    {
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));
            Yii::$app->end();
        }
    }
}
