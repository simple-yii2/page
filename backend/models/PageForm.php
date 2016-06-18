<?php

namespace page\backend\models;

use Yii;
use yii\base\Model;

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
	 * @var string Lead text.
	 */
	public $lead;

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
			'lead' => Yii::t('page', 'Lead text'),
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
			[['lead', 'content'], 'string'],
		];
	}

	/**
	 * @inheritdoc
	 * Set default values
	 */
	public function init() {
		parent::init();
		
		if ($this->item !== null) {
			$this->setAttributes([
				'title' => $this->item->title,
				'active' => $this->item->active,
				'lead' => $this->item->lead,
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
			'lead' => $this->lead,
			'content' => $this->content,
		], false);

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

		$this->item->setAttributes([
			'title' => $this->title,
			'active' => $this->active,
			'lead' => $this->lead,
			'content' => $this->content,
		], false);

		$success = $this->item->save(false);

		return $success;
	}

}
