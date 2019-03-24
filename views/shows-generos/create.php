<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ShowsGeneros */

$this->title = 'Create Shows Generos';
$this->params['breadcrumbs'][] = ['label' => 'Shows Generos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-generos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
