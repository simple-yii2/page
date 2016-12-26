<?php

namespace cms\page\backend\models;

use Yii;
use yii\base\Model;

use cms\page\common\models\Page;

/**
 * Page editting form
 */
class PageForm extends Model
{

	/**
	 * @var string Page title.
	 */
	public $title;

	/**
	 * @var boolean Active.
	 */
	public $active;

	/**
	 * @var string Page content.
	 */
	public $content;

	/**
	 * @var cms\page\common\models\Page Page model
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param cms\page\common\models\Page $object 
	 */
	public function __construct(\cms\page\common\models\Page $object, $config = [])
	{
		$this->_object = $object;

		//attributes
		$this->title = $object->title;
		$this->active = $object->active == 0 ? '0' : '1';
		$this->content = $object->content;

		//file caching
		Yii::$app->storage->cacheObject($object);

		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'title' => Yii::t('page', 'Title'),
			'active' => Yii::t('page', 'Active'),
			'content' => Yii::t('page', 'Content'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['title', 'string', 'max' => 100],
			['active', 'boolean'],
			['content', 'string'],
		];
	}

	/**
	 * Save object using model attributes
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$object = $this->_object;
		$isNewRecord = $object->getIsNewRecord();

		$object->title = $this->title;
		$object->active = $this->active == 1;
		$object->content = $this->content;
		$object->modifyDate = gmdate('Y-m-d H:i:s');

		Yii::$app->storage->storeObject($object);

		if (!$object->save(false))
			return false;

		if ($isNewRecord) {
			$object->makeAlias();
			$object->update(false, ['alias']);
		}

		return true;
	}

}
