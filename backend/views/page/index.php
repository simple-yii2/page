<?php

use yii\grid\GridView;
use yii\helpers\Html;

$title = Yii::t('page', 'Pages');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?php if ($canAddPage): ?>
<div class="btn-toolbar" role="toolbar">
	<?= Html::a(Yii::t('page', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
</div>
<?php endif; ?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $model,
	'summary' => '',
	'tableOptions' => ['class' => 'table table-condensed'],
	'rowOptions' => function ($model, $key, $index, $grid) {
		return !$model->active ? ['class' => 'warning'] : [];
	},
	'columns' => [
		'title',
		[
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 75px;'],
			'template' => '{link} {update} {delete}',
			'buttons' => [
				'link' => function($url, $model, $key) {
					$title = Yii::t('page', 'Link');

					return Html::a('<span class="glyphicon glyphicon-link"></span>', ['/page/page/index', 'alias' => $model->alias], [
						'title' => $title,
						'aria-label' => $title,
						'data-pjax' => 0,
					]);
				},
			],
		],
	],
]) ?>
