<?php namespace dektrium\user\controllers;

use dektrium\user\models\UserSearch;
use yii\web\Controller;

/**
 * AdminController allows you to administrate users.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class AdminController extends Controller
{
	public function actionIndex()
	{
		$searchModel  = new UserSearch();
		$dataProvider = $searchModel->search($_GET);

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}
}