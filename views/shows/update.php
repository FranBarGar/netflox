<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */

$this->title = 'Actualizar Shows: ' . $model->titulo;
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shows-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formUpdate', [
        'model' => $model,
        'participantesProvider' => $participantesProvider,
        'archivosProvider' => $archivosProvider,
        'listaTipos' => $listaTipos,
        'listaGeneros' => $listaGeneros,
        'listaPersonas' => $listaPersonas,
        'listaRoles' => $listaRoles,
    ]) ?>
</div>
