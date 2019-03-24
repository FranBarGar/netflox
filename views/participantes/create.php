<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Participantes */

$this->title = 'Create Participantes';
$this->params['breadcrumbs'][] = ['label' => 'Participantes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="participantes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
