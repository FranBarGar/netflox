<?php

use kartik\select2\Select2;
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

    <?= $form->field($model, 'titulo') ?>

    <div class="form-row">
        <div class="form-group col-md-6">
            <?=
            $form->field($model, 'tipo_id')
                ->widget(Select2::class, ['data' => $listaTipos,
                    'options' => [
                        'placeholder' => 'Selecciona un tipo de show a buscar...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ?>
        </div>
        <div class="form-group col-md-6">
            <?=
            $form->field($model, 'listaGeneros')
                ->widget(Select2::class, [
                    'data' => $listaGeneros,
                    'options' => [
                        'placeholder' => 'Seleccione los generos a buscar en los shows...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                    ],
                ])
            ?>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group col-md-6">
            <?=
            $form->field($model, 'orderBy')
                ->widget(Select2::class, [
                    'data' => $orderBy,
                    'options' => [
                        'placeholder' => 'Seleccione el tipo de ordenación...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])
            ?>
        </div>
        <div class="form-group col-md-2">
            <?=
            $form->field($model, 'orderType')
                ->widget(Select2::class, ['data' => $orderType,
                    'options' => [
                        'placeholder' => 'Selecciona un tipo de show a buscar...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
