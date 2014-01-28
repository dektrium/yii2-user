<?php namespace dektrium\user\controllers;

use dektrium\user\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AdminController allows you to administrate users.
 *
 * @property \dektrium\user\Module $module
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

	/**
	 * Deletes an existing User model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the User model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return \dektrium\user\models\User the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		/** @var \dektrium\user\models\User $user */
		$user = $this->module->factory->createQuery()->where(['id' => $id])->one();
		if ($id !== null && $user !== null) {
			return $user;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}