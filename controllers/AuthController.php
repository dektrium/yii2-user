<?php namespace dektrium\user\controllers;

use dektrium\user\models\User;
use yii\web\Controller;

/**
 * Controller that manages user authentication process.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AuthController extends Controller
{
    /**
     * Displays the login page.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $model = new User(['scenario' => 'login']);

        if ($model->load($_POST) && $model->login()) {
            return $this->redirect(\Yii::$app->getUser()->getReturnUrl());
        }

        return $this->render('login', ['model' => $model]);
    }
}
 