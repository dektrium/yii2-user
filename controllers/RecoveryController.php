<?php namespace dektrium\user\controllers;

use dektrium\user\forms\Recovery;
use yii\db\ActiveQuery;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends Controller
{
    /**
     * Displays page where user can request new recovery message.
     *
     * @return string
     */
    public function actionRequest()
    {
        $model = \Yii::createObject([
            'class' => $this->module->recoveryForm,
            'scenario' => 'request',
        ]);

        if ($model->load($_POST) && $model->sendRecoveryMessage()) {
            return $this->render('messageSent');
        }

        return $this->render('request', [
            'model' => $model
        ]);
    }

    /**
     * Displays page where user can reset password.
     *
     * @param $id
     * @param $token
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($id, $token)
    {
        $query = new ActiveQuery(['modelClass' => \Yii::$app->getUser()->identityClass]);
        /** @var \dektrium\user\models\User $user */
        $user = $query->where(['id' => $id, 'recovery_token' => $token])->one();
        if ($user === null) {
            throw new NotFoundHttpException();
        } elseif ($user->getIsRecoveryPeriodExpired()) {
            return $this->render('invalidToken');
        }
        $model = \Yii::createObject([
            'class' => $this->module->recoveryForm,
            'scenario' => 'reset',
            'identity' => $user
        ]);

        if ($model->load($_POST) && $model->reset()) {
            return $this->render('finish');
        }

        return $this->render('reset', [
            'model' => $model
        ]);
    }
}