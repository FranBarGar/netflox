<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GestoresArchivos */

$this->title = 'Update Gestores Archivos: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gestores Archivos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gestores-archivos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
