<?php

use yii\helpers\Html;

$title = $model->title;

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [$title];

?>
<h1><?= Html::encode($title) ?></h1>

<?= $model->content ?>
