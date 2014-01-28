<?php namespace dektrium\user\controllers;

use yii\web\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * RecoveryController manages password recovery process.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends Controller
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
						'actions' => ['request', 'reset', 'captcha'],
						'roles' => ['?']
					],
				]
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action)
	{
		if (parent::beforeAction($action)) {
			if (!$this->module->recoverable) {
				throw new NotFoundHttpException('Disabled by administrator');
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Displays page where user can request new recovery message.
	 *
	 * @return string
	 * @throws \yii\web\NotFoundHttpException
	 */
	public function actionRequest()
	{
		$model = $this->module->factory->createForm('recovery', ['scenario' => 'request']);

		if ($model->load($_POST) && $model->validate()) {
			$model->user->sendRecoveryMessage();
			return $this->render('messageSent', [
				'model' => $model
			]);
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
		/** @var \dektrium\user\models\User $user */
		$query = $this->module->factory->createQuery();
		$user  = $query->where(['id' => $id, 'recovery_token' => $token])->one();
		if ($user === null) {
			throw new NotFoundHttpException();
		} elseif ($user->getIsRecoveryPeriodExpired()) {
			return $this->render('invalidToken');
		}

		$model = $this->module->factory->createForm('recovery', [
			'scenario' => 'reset',
			'user'     => $user
		]);

		if ($model->load($_POST) && $model->reset()) {
			return $this->render('finish');
		}

		return $this->render('reset', [
			'model' => $model
		]);
	}
}