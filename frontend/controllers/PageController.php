<?php

namespace page\frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use page\common\models\Page;

/**
 * Page frontend controller
 */
class PageController extends Controller
{

	/**
	 * Show page contents
	 * @param string $alias 
	 * @return void
	 */
	public function actionIndex($alias)
	{
		$model = Page::findByAlias($alias);
		if ($model === null || !$model->active)
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));

		return $this->render('index', [
			'model' => $model,
		]);
	}

}
