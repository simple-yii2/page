<?php

namespace page\common\models;

use Yii;
use yii\db\ActiveRecord;

use helpers\Translit;

/**
 * Page acrive record
 */
class Page extends ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'Page';
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'alias' => Yii::t('page', 'Alias'),
			'title' => Yii::t('page', 'Title'),
			'active' => Yii::t('page', 'Active'),
			'lead' => Yii::t('page', 'Lead text'),
			'content' => Yii::t('page', 'Content'),
		];
	}

	/**
	 * Find page by alias
	 * @param sring $alias Page alias or id.
	 * @return Page
	 */
	public static function findByAlias($alias) {
		$model = static::findOne(['alias' => $alias]);
		if ($model === null)
			$model = static::findOne(['id' => $alias]);

		return $model;
	}

	/**
	 * Making page alias from title and id
	 * @return void
	 */
	public function makeAlias()
	{
		$this->alias = Translit::t($this->title . '-' . $this->id);
	}

}
