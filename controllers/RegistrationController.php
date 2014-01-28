<?php namespace dektrium\user\controllers;

use yii\web\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Controller that manages user registration process.
 *
 * @property \dektrium\user\Module $module
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
						'actions' => ['confirm', 'resend', 'captcha'],
						'roles' => ['?', '@']
					],
				]
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action)
	{
		if (parent::beforeAction($action)) {
			if (!$this->module->confirmable && in_array($action->id, ['confirm', 'resend'])) {
				throw new NotFoundHttpException('Disabled by administrator');
			}
			return true;
		} else {
			return false;
		}
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
	 * Displays the registration page.
	 *
	 * @return string
	 */
	public function actionRegister()
	{
		$model = $this->module->factory->createUser();
		$model->scenario = 'register';

		if ($model->load($_POST) && $model->register()) {
			return $this->render('success', [
				'model' => $model
			]);
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
		$query = $this->module->factory->createQuery();
		/** @var \dektrium\user\models\User $user */
		$user = $query->where(['id' => $id, 'confirmation_token' => $token])->one();
		if ($user === null) {
			throw new NotFoundHttpException('User not found');
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
		$model = $this->module->factory->createForm('resend');

		if ($model->load($_POST) && $model->validate()) {
			$model->getUser()->resend();
			return $this->render('success', [
				'model' => $model
			]);
		}

		return $this->render('resend', [
			'model' => $model
		]);
	}
}
