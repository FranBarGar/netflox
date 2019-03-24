<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ShowsGeneros */

$this->title = 'Update Shows Generos: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shows Generos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shows-generos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
