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

	/**
	 * Parsing html for files in <img> and <a>.
	 * @param string $content 
	 * @return string[]
	 */
	protected function getFilesFromContent($content)
	{
		if (preg_match_all('/(?:src|href)="([^"]+)"/i', $content, $matches))
			return $matches[1];

		return [];		
	}

	/**
	 * Replacing files with new saved with storage
	 * @param array $files Keys is old file name and value is new file name
	 * @return void
	 */
	protected function setFiles($files)
	{
		$content = $this->content;
		foreach ($files as $from => $to) {
			$content = str_replace($from, $to, $content);
		}

		$this->content = $content;
	}

	/**
	 * Updating files. Remove old and store new files.
	 * @return void
	 */
	protected function storeFiles()
	{
		if (isset(Yii::$app->storage)) {
			if (($store = Yii::$app->storage) instanceof \storage\components\StorageInterface) {
				$files = $store->update(
					$this->getFilesFromContent($this->getOldAttribute('content')),
					$this->getFilesFromContent($this->content)
				);
				$this->setFiles($files);
			}
		}
	}

	/**
	 * Remove old files when object deletes
	 * @return void
	 */
	protected function deleteFiles()
	{
		if (isset(Yii::$app->store)) {
			if (($store = Yii::$app->store) instanceof \storage\components\StorageInterface) {
				$files = $store->update($this->getFilesFromContent($this->content), []);
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert)) {
			$this->storeFiles();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function beforeDelete()
	{
		if (parent::beforeDelete()) {
			$this->deleteFiles();
			return true;
		} else {
			return false;
		}
	}

}
