<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Votos */

$this->title = 'Create Votos';
$this->params['breadcrumbs'][] = ['label' => 'Votos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="votos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
