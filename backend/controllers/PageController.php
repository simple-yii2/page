<?php

namespace page\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

use page\backend\models\PageForm;
use page\backend\models\PageSearch;

/**
 * Page manage controller
 */
class PageController extends Controller
{

	/**
	 * Access control
	 * @return array
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					['allow' => true, 'roles' => ['page']],
				],
			],
		];
	}

	/**
	 * Page list
	 * @return void
	 */
	public function actionIndex() {
		$model = new PageSearch;

		return $this->render('index', [
			'dataProvider'=>$model->search(Yii::$app->getRequest()->get()),
			'model'=>$model,
		]);
	}

	/**
	 * Page creating
	 * @return void
	 */
	public function actionCreate() {
		$model = new PageForm;

		if ($model->load(Yii::$app->getRequest()->post()) && $model->create()) {
			Yii::$app->session->setFlash('success', Yii::t('page', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

}
