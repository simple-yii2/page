<?php

namespace cms\page\backend;

use Yii;

use cms\components\BackendModule;

/**
 * Page backend module
 */
class Module extends BackendModule {

	/**
	 * @inheritdoc
	 */
	public static function moduleName()
	{
		return 'page';
	}

	/**
	 * @inheritdoc
	 */
	public static function cmsSecurity()
	{
		$auth = Yii::$app->getAuthManager();
		if ($auth->getRole('Page') === null) {
			//role
			$role = $auth->createRole('Page');
			$auth->add($role);
		}
	}

	/**
	 * @inheritdoc
	 */
	public static function cmsMenu($base)
	{
		if (!Yii::$app->user->can('Page'))
			return [];

		return [
			['label' => Yii::t('page', 'Pages'), 'url' => ["$base/page/page/index"]],
		];
	}

}
