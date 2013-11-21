<?php namespace dektrium\user\controllers;

use dektrium\user\models\ResendForm;
use dektrium\user\models\User;
use yii\db\ActiveQuery;
use yii\web\Controller;
use yii\web\HttpException;

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

    public function actionConfirm($id, $token)
    {
        $query = new ActiveQuery(['modelClass' => \Yii::$app->getUser()->identityClass]);
        /** @var \dektrium\user\models\User $user */
        $user = $query->where(['id' => $id, 'confirmation_token' => $token])->one();
        if ($user === null) {
            throw new HttpException(404, 'User not found');
        }
        if ($user->confirm()) {
            return $this->render('finish');
        } else {
            return $this->render('invalidToken');
        }
    }

    public function actionResend()
    {
        $model = new ResendForm();

        if ($model->load($_POST) && $model->resend()) {
            return $this->render('success');
        }

        return $this->render('resend', [
            'model' => $model
        ]);
    }
}