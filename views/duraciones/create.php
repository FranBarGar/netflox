<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Duraciones */

$this->title = 'Create Duraciones';
$this->params['breadcrumbs'][] = ['label' => 'Duraciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="duraciones-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
