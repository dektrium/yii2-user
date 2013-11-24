<?php namespace dektrium\user\controllers;

use dektrium\user\models\ResendForm;
use yii\db\ActiveQuery;
use yii\web\AccessControl;
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
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['register'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm', 'resend'],
                        'roles' => ['?', '@']
                    ],
                ]
            ],
        ];
    }

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

    /**
     * Confirms user's account.
     *
     * @param $id
     * @param $token
     * @return string
     * @throws \yii\web\HttpException
     */
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

    /**
     * Displays page where user can request new confirmation token.
     *
     * @return string
     */
    public function actionResend()
    {
        $model = \Yii::createObject([
            'class' => $this->module->resendForm
        ]);

        if ($model->load($_POST) && $model->resend()) {
            return $this->render('success');
        }

        return $this->render('resend', [
            'model' => $model
        ]);
    }
}
