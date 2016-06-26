<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$options = [
	'minHeight' => 250,
];

if (isset(Yii::$app->storage) && (Yii::$app->storage instanceof storage\components\StorageInterface)) {
	$options['imageUpload'] = Url::toRoute('image');
}

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
	'options' => ['class' => 'page-form'],
]); ?>

	<?= $form->field($model, 'active')->checkbox() ?>

	<?= $form->field($model, 'title') ?>

	<?= $form->field($model, 'lead')->textarea(['rows' => 5]) ?>

	<?= $form->field($model, 'content')->widget(\yii\imperavi\Widget::className(), [
		'options' => $options,
		'plugins' => [
			'fullscreen',
		],
	]) ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('page', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('page', 'Cancel'), ['index'], ['class' => 'btn btn-link']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
