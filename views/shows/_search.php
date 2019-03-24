<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShowsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shows-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'titulo') ?>

    <?= $form->field($model, 'sinopsis') ?>

    <?= $form->field($model, 'lanzamiento') ?>

    <?= $form->field($model, 'duracion') ?>

    <?php // echo $form->field($model, 'imagen_id') ?>

    <?php // echo $form->field($model, 'trailer_id') ?>

    <?php // echo $form->field($model, 'tipo_id') ?>

    <?php // echo $form->field($model, 'show_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
