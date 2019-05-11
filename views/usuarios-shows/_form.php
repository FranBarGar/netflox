<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosShows */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-shows-form">

    <?php
    $form = ActiveForm::begin([
        'action' => Url::to([
            'usuarios-shows/create',
            'id' => $model->show_id,
        ])
    ]);
    ?>

    <?= $form->field($model, 'usuario_id')->hiddenInput(['value' => $model->usuario_id])->label(false) ?>
    <?= $form->field($model, 'show_id')->hiddenInput(['value' => $model->show_id])->label(false) ?>
    <?= $form->field($model, 'accion_id')
        ->widget(Select2::class, [
            'data' => $listaAcciones,
            'options' => [
                'placeholder' => 'Seleccione la accion para este show...',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
