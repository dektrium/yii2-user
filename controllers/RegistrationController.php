<?php namespace dektrium\user\controllers;

use yii\web\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;

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
						'actions' => ['confirm', 'resend'],
						'roles' => ['?', '@']
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
	 * Displays the registration page.
	 *
	 * @return string
	 */
	public function actionRegister()
	{
		$model = $this->module->factory->createForm('registration');

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
		$query = $this->module->factory->createQuery();
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
		$model = $this->module->factory->createForm('resend');

		if ($model->load($_POST) && $model->resend()) {
			return $this->render('success');
		}

		return $this->render('resend', [
			'model' => $model
		]);
	}
}
