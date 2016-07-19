<?php

namespace page\backend\models;

use Yii;
use yii\base\Model;

use page\common\models\Page;

/**
 * Page editting form
 */
class PageForm extends Model {

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
	 * @var page\common\models\Page Page model
	 */
	public $item;

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
	 * @inheritdoc
	 * Set default values
	 */
	public function init() {
		parent::init();

		$this->active = true;
		
		if ($this->item !== null) {
			$this->setAttributes([
				'title' => $this->item->title,
				'active' => $this->item->active,
				'content' => $this->item->content,
			], false);
		}
	}

	/**
	 * Page creation
	 * @return boolean
	 */
	public function create() {
		if (!$this->validate())
			return false;

		$this->item = new Page;

		$this->item->setAttributes([
			'title' => $this->title,
			'active' => $this->active,
			'modifyDate' => gmdate('Y-m-d H:i:s'),
			'content' => $this->content,
		], false);

		Yii::$app->storage->storeObject($this->item);

		$success = $this->item->save(false);

		if ($success) {
			$this->item->makeAlias();
			$this->item->update(false, ['alias']);
		}

		return $success;
	}

	/**
	 * Page updating
	 * @return boolean
	 */
	public function update() {
		if ($this->item === null)
			return false;

		if (!$this->validate())
			return false;

		$this->item->setAttributes([
			'title' => $this->title,
			'active' => $this->active,
			'modifyDate' => gmdate('Y-m-d H:i:s'),
			'content' => $this->content,
		], false);

		Yii::$app->storage->storeObject($this->item);

		$success = $this->item->save(false);

		return $success;
	}

}
