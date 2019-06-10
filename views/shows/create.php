<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */

$this->title = 'Crear Shows';
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formCreate', [
        'model' => $model,
        'listaTipos' => $listaTipos,
        'listaGeneros' => $listaGeneros,
        'listaPersonas' => $listaPersonas,
        'listaRoles' => $listaRoles,
    ]) ?>

</div>
