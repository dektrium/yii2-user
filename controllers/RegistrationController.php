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
     */
    public function actionRegister()
    {
        $model = new User(['scenario' => 'signup']);

        if ($model->load($_POST) && $model->register()) {
            return $this->redirect($this->module->params['afterRegistrationRedirectUrl']);
        }

        return $this->render('register', ['model' => $model]);
    }
}