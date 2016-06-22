<?php

namespace page\backend;

use Yii;

/**
 * Page backend module
 */
class Module extends \yii\base\Module {

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->checkDatabase();
		self::addTranslation();
	}

	/**
	 * Database checking
	 * @return void
	 */
	protected function checkDatabase()
	{
		//schema
		$db = Yii::$app->db;
		$filename = dirname(__DIR__) . '/schema/' . $db->driverName . '.sql';
		$sql = explode(';', file_get_contents($filename));
		foreach ($sql as $s) {
			if (trim($s) !== '')
				$db->createCommand($s)->execute();
		}

		//rbac
		$auth = Yii::$app->getAuthManager();
		if ($auth->getRole('page') === null) {
			//page role
			$page = $auth->createRole('page');
			$auth->add($page);
		}
	}

	/**
	 * Adding translation to i18n
	 * @return void
	 */
	protected static function addTranslation()
	{
		if (!isset(Yii::$app->i18n->translations['page'])) {
			Yii::$app->i18n->translations['page'] = [
				'class'=>'yii\i18n\PhpMessageSource',
				'sourceLanguage'=>'en-US',
				'basePath'=>'@page/messages',
			];
		}
	}

	/**
	 * Making main menu item of module
	 * @param string $base route base
	 * @return array
	 */
	public static function getMenu($base)
	{
		self::addTranslation();

		if (Yii::$app->user->can('page')) {
			return [
				['label' => Yii::t('page', 'Pages'), 'url' => ["$base/page/page/index"]],
			];
		}
		
		return [];
	}

}
