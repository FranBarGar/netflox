<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GestoresArchivos */

$this->title = 'Create Gestores Archivos';
$this->params['breadcrumbs'][] = ['label' => 'Gestores Archivos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gestores-archivos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
