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
use dektrium\user\models\RecoveryForm;
use dektrium\user\models\Token;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use dektrium\user\traits\AjaxValidationTrait;

/**
 * RecoveryController manages password recovery process.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends Controller
{
    use AjaxValidationTrait;
    
    /** @var Finder */
    protected $finder;

    /**
     * @param string           $id
     * @param \yii\base\Module $module
     * @param Finder           $finder
     * @param array            $config
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
                    ['allow' => true, 'actions' => ['request', 'reset'], 'roles' => ['?']],
                ]
            ],
        ];
    }

    /**
     * Shows page where user can request password recovery.
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRequest()
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException;
        }

        $model = \Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => 'request',
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Recovery message sent'),
                'module' => $this->module,
            ]);
        }

        return $this->render('request', [
            'model' => $model,
        ]);
    }

    /**
     * Displays page where user can reset password.
     * @param  integer $id
     * @param  string  $code
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($id, $code)
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException;
        }

        /** @var Token $token */
        $token = $this->finder->findToken(['user_id' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY])->one();

        if ($token === null || $token->isExpired || $token->user === null) {
            \Yii::$app->session->setFlash('danger', \Yii::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.'));
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Invalid or expired link'),
                'module' => $this->module,
            ]);
        }

        $model = \Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => 'reset',
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Password has been changed'),
                'module' => $this->module,
            ]);
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}
