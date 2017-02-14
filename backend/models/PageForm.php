<?php

namespace cms\page\backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;

use cms\page\common\models\Page;

/**
 * Page editting form
 */
class PageForm extends Model
{

	/**
	 * @var boolean Active.
	 */
	public $active;

	/**
	 * @var string Page title.
	 */
	public $title;

	/**
	 * @var string Alias
	 */
	public $alias;

	/**
	 * @var string Page content.
	 */
	public $content;

	/**
	 * @var Page Page model
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Page $object 
	 */
	public function __construct(Page $object, $config = [])
	{
		$this->_object = $object;

		//attributes
		$this->active = $object->active == 0 ? '0' : '1';
		$this->title = $object->title;
		$this->alias = $object->alias;
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
			'active' => Yii::t('page', 'Active'),
			'title' => Yii::t('page', 'Title'),
			'alias' => Yii::t('page', 'Alias'),
			'content' => Yii::t('page', 'Content'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			['active', 'boolean'],
			['title', 'string', 'max' => 100],
			['alias', 'match', 'pattern' => '/^[a-z0-9\-_]*$/'],
			['content', 'string'],
			['title', 'required'],
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

		$object->active = $this->active == 1;
		$object->title = $this->title;
		$object->alias = $this->alias;
		$object->content = HtmlPurifier::process($this->content, [
			'HTML.SafeIframe' => true,
			'URI.SafeIframeRegexp' => '%^(?:http:)?//(?:www.youtube.com/embed/|player.vimeo.com/video/)%',
		]);
		$object->modifyDate = gmdate('Y-m-d H:i:s');

		Yii::$app->storage->storeObject($object);

		if (!$object->save(false))
			return false;

		if (empty($object->alias)) {
			$object->makeAlias();
			$object->update(false, ['alias']);
		}

		return true;
	}

}
