<?php

namespace page\backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\UnsupportedMediaTypeHttpException;

use page\backend\models\PageForm;
use page\backend\models\PageSearch;
use page\common\models\Page;

/**
 * Page manage controller.
 */
class PageController extends Controller
{

	/**
	 * Access control.
	 * @return array
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					['allow' => true, 'roles' => ['Page']],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 * Disable csrf validation for image uploading
	 */
	public function beforeAction($action)
	{
		if ($action->id == 'image')
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	/**
	 * Page list.
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new PageSearch;

		return $this->render('index', [
			'dataProvider' => $model->search(Yii::$app->getRequest()->get()),
			'model' => $model,
		]);
	}

	/**
	 * Page creating.
	 * @return void
	 */
	public function actionCreate()
	{
		$model = new PageForm;

		if ($model->load(Yii::$app->getRequest()->post()) && $model->create()) {
			Yii::$app->session->setFlash('success', Yii::t('page', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Page updating.
	 * @param integer $id Page id.
	 * @return void
	 */
	public function actionUpdate($id)
	{
		$item = Page::findOne($id);
		if ($item === null)
			throw new BadRequestHttpException(Yii::t('page', 'Page not found.'));

		$model = new PageForm(['item' => $item]);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->update()) {
			Yii::$app->session->setFlash('success', Yii::t('page', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Page deleting.
	 * @param integer $id Page id.
	 * @return void
	 */
	public function actionDelete($id)
	{
		$item = Page::findOne($id);
		if ($item === null)
			throw new BadRequestHttpException(Yii::t('page', 'Page not found.'));

		if ($item->delete()) {
			Yii::$app->storage->removeObject($item);
			
			Yii::$app->session->setFlash('success', Yii::t('page', 'Page deleted successfully.'));
		}

		return $this->redirect(['index']);
	}

	/**
	 * Uploading images.
	 * @return void
	 */
	public function actionImage()
	{
		$name = Yii::$app->storage->prepare('file', [
			'image/png',
			'image/jpg',
			'image/gif',
			'image/jpeg',
			'image/pjpeg',
		]);

		if ($name === false)
			throw new BadRequestHttpException(Yii::t('page', 'Error occurred while image uploading.'));

		return Json::encode([
			['filelink' => $name],
		]);
	}

}
