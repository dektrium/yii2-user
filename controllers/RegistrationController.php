<?php namespace dektrium\user\controllers;

use dektrium\user\models\User;
use yii\web\Controller;

/**
 * Controller that manages user registration process.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationController extends Controller
{
    /**
     * Displays the registration page.
     *
     * @return string
     */
    public function actionRegister()
    {
        $model = \Yii::createObject([
            'class'    => \Yii::$app->getUser()->identityClass,
            'scenario' => 'register'
        ]);

        if ($model->load($_POST) && $model->register()) {
            return $this->render('success');
        }

        return $this->render('register', [
            'model' => $model
        ]);
    }
}