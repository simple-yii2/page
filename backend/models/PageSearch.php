<?php

namespace cms\page\backend\models;

use Yii;
use yii\data\ActiveDataProvider;

use cms\page\common\models\Page;

/**
 * Page search model
 */
class PageSearch extends Page
{

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'title' => Yii::t('page', 'Title'),
		];
	}

	/**
	 * Search rules
	 * @return array
	 */
	public function rules() {
		return [
			['title', 'string'],
		];
	}

	/**
	 * Search function
	 * @param array $params Attributes array
	 * @return yii\data\ActiveDataProvider
	 */
	public function search($params) {
		//ActiveQuery
		$query = static::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		//return data provider if no search
		if (!($this->load($params) && $this->validate()))
			return $dataProvider;

		//search
		$query->andFilterWhere(['like', 'title', $this->title]);

		return $dataProvider;
	}

}
