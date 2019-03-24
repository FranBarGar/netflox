<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Generos */

$this->title = 'Create Generos';
$this->params['breadcrumbs'][] = ['label' => 'Generos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="generos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
