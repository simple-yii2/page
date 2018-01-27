<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$settings = [
	'minHeight' => 250,
	'toolbarFixedTopOffset' => 50,
	'plugins' => [
		'fullscreen',
		'video',
		'table',
	],
];

if (isset(Yii::$app->storage) && (Yii::$app->storage instanceof dkhlystov\storage\components\StorageInterface)) {
	$settings['imageUpload'] = Url::toRoute('image');
	$settings['fileUpload'] = Url::toRoute('file');
}

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'fieldConfig' => [
		'horizontalCssClasses' => [
			'wrapper' => 'col-sm-9',
		],
	],
	'enableClientValidation' => false,
]); ?>

	<?= $form->field($model, 'active')->checkbox() ?>

	<?= $form->field($model, 'title') ?>

	<?= $form->field($model, 'alias') ?>

	<?= $form->field($model, 'content')->widget(\vova07\imperavi\Widget::className(), ['settings' => $settings]) ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('page', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('page', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
