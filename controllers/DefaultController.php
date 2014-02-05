<?php namespace dektrium\user\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class DefaultController extends Controller
{
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

	public function actionIndex()
	{
		if (defined('YII_DEBUG') && YII_DEBUG) {
			return $this->render('index');
		} else {
			throw new NotFoundHttpException('Page not found');
		}
	}
}