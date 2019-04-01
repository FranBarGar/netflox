<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */

$this->title = 'Update Shows: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shows-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'listaTipos' => $listaTipos,
        'listaGeneros' => $listaGeneros,
    ]) ?>
<!--TODO-->
<!--
    if (isset($model->imagen->link)) {
        $preview = ['pluginOptions' => [
            'initialPreview'=>[
                $model->imagen->link
            ],
            'initialPreviewAsData'=>true,
            'initialCaption'=> $model->titulo,
            'maxFileSize'=>2800
            ]
        ];
    } else {
        $preview = [];
    }-->

</div>
