<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */

$this->title = 'Create Shows';
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'listaTipos' => $listaTipos,
        'listaGeneros' => $listaGeneros,
        'listaGestores' => $listaGestores,
        'listaPersonas' => $listaPersonas,
        'listaRoles' => $listaRoles,
    ]) ?>

</div>
