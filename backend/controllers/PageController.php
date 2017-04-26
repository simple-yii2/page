<?php

namespace cms\page\backend\controllers;

use Yii;
use yii\base\NotSupportedException;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

use cms\page\backend\models\PageForm;
use cms\page\backend\models\PageSearch;
use cms\page\common\models\Page;

/**
 * Page manage controller
 */
class PageController extends Controller
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
					['allow' => true, 'roles' => ['Page']],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 * Disable csrf validation for image and file uploading
	 */
	public function beforeAction($action)
	{
		if ($action->id == 'image' || $action->id == 'file')
			$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	/**
	 * List
	 * @return string
	 */
	public function actionIndex()
	{
		$model = new PageSearch;

		return $this->render('index', [
			'dataProvider' => $model->search(Yii::$app->getRequest()->get()),
			'model' => $model,
			'canAddPage' => $this->canAddPage(),
		]);
	}

	/**
	 * Create
	 * @return string
	 */
	public function actionCreate()
	{
		if (!$this->canAddPage())
			throw new NotSupportedException('You have exceeded the maximum number of pages.');

		$model = new PageForm(new Page);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('page', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Update
	 * @param integer $id Page id.
	 * @return string
	 */
	public function actionUpdate($id)
	{
		$object = Page::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('page', 'Page not found.'));

		$model = new PageForm($object);

		if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', Yii::t('page', 'Changes saved successfully.'));
			return $this->redirect(['index']);
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Delete
	 * @param integer $id Page id.
	 * @return string
	 */
	public function actionDelete($id)
	{
		$object = Page::findOne($id);
		if ($object === null)
			throw new BadRequestHttpException(Yii::t('page', 'Page not found.'));

		if ($object->delete()) {
			Yii::$app->storage->removeObject($object);
			
			Yii::$app->session->setFlash('success', Yii::t('page', 'Page deleted successfully.'));
		}

		return $this->redirect(['index']);
	}

	/**
	 * Image upload
	 * @return string
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

	/**
	 * File upload
	 * @return string
	 */
	public function actionFile()
	{
		$name = Yii::$app->storage->prepare('file', [
			'image',
			'application/zip',
			'application/msword',
			'application/vnd.ms-office',
			'application/vnd.openxmlformats-officedocument',
			'text/rtf',
			'application/pdf',
		]);

		if ($name === false)
			throw new BadRequestHttpException(Yii::t('page', 'Error occurred while file uploading.'));

		return Json::encode([
			['filelink' => $name],
		]);
	}

	/**
	 * Determining if there are a restrictions of adding page
	 * @return boolean
	 */
	private function canAddPage()
	{
		return $this->module->maxCount === null || Page::find()->count() < $this->module->maxCount;
	}

}
