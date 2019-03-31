<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */
/* @var $form yii\widgets\ActiveForm */

$url = \yii\helpers\Url::to(['shows/ajax-lista-padres']);
$js = <<<EOJS
    div = $('div.field-shows-show_id');
    select = div.children('select');
    duracion = $('span#tipo_duracion');
    
    div.hide();
    
    function changeTipo(e) 
    {
        select.empty();
        
        var tipoId = this.value;
        
        if (tipoId == '') {
            div.hide();
        } else {
            $.ajax({
            url: '$url',
            data: { id: tipoId },
            success: function (data) {
                if (data === false) {
                    div.hide();
                } else {
                    div.show();
                    select.append('<option value>Selecciona el show al que pertenece...</option>');
                    $.each(data, (key, value) => {
                        select.append('<option value="'+key+'">'+value+'</option>');
                    });
                }
            }
            });
        }
        
    }
EOJS;
$this->registerJs($js);

?>

<div class="shows-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tipo_id')
        ->widget(\kartik\select2\Select2::class, ['data' => $listaTipos,
            'options' => [
                'placeholder' => 'Selecciona un tipo de show...',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents' => [
                "change" => "changeTipo",
            ]
        ])
    ?>
    <!--    TODO: Segun el tipo elegido, mostrar, ocultar, o cambair el contenido de este campo -->
    <?= $form->field($model, 'show_id')
        ->widget(\kartik\select2\Select2::class, ['data' => $listaTipos,
            'options' => [
                'hidden' => true,
                'placeholder' => 'Selecciona el show al que pertenece...',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])
    ?>
    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sinopsis')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'lanzamiento')
        ->widget(\kartik\date\DatePicker::classname(), [
            'options' => ['placeholder' => 'Enter birth date ...'],
            'pluginOptions' => [
                'autoclose' => true
            ]
        ])
    ?>
    <?= $form->field($model, 'duracion')->textInput()->label('Duracion en <span id="tipo_duracion"></span>') ?>


    <?= $form->field($model, 'imagen_id')->textInput() ?>
    <?= $form->field($model, 'trailer_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
