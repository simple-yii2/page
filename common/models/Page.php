<?php

namespace cms\page\common\models;

use yii\db\ActiveRecord;

use helpers\Translit;
use dkhlystov\storage\components\StoredInterface;

/**
 * Page active record
 */
class Page extends ActiveRecord implements StoredInterface
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'Page';
	}

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$this->active = true;
	}

	/**
	 * Find page by alias
	 * @param sring $alias Page alias or id.
	 * @return Page
	 */
	public static function findByAlias($alias)
	{
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
	 * @inheritdoc
	 */
	public function getOldFiles()
	{
		return $this->getFilesFromContent($this->getOldAttribute('content'));
	}

	/**
	 * @inheritdoc
	 */
	public function getFiles()
	{
		return $this->getFilesFromContent($this->content);
	}

	/**
	 * @inheritdoc
	 */
	public function setFiles($files)
	{
		$content = $this->content;
		foreach ($files as $from => $to) {
			$content = str_replace($from, $to, $content);
		}

		$this->content = $content;
	}

}
