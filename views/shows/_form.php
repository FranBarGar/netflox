<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */
/* @var $form yii\widgets\ActiveForm */

$url = \yii\helpers\Url::to(['shows/ajax-create-info']);
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
                data = JSON.parse(data);
                padres = data[1];
                console.log(data);
                if (padres === false) {
                    div.hide();
                } else {
                    div.show();
                    select.append('<option value>Selecciona el show al que pertenece...</option>');
                    $.each(padres, (key, value) => {
                        select.append('<option value="'+key+'">'+value+'</option>');
                    });
                }
                $('#tipo_duracion').html(data[0]);
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
        ->widget(\kartik\select2\Select2::class, [
            'data' => $listaTipos,
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
    <?= $form->field($model, 'show_id')
        ->widget(\kartik\select2\Select2::class, [
            'options' => [
                'hidden' => true,
                'placeholder' => 'Selecciona el show al que pertenece...',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])
    ?>
    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true, 'placeholder' => "Introduzca el titulo del show..."]) ?>
    <?= $form->field($model, 'sinopsis')->textarea(['rows' => 6, 'placeholder' => "Introduzca la sinopsis del show..."]) ?>
    <?= $form->field($model, 'lanzamiento')
        ->widget(\kartik\date\DatePicker::classname(), [
            'options' => ['placeholder' => 'Selecciona la fecha de estreno...'],
            'pluginOptions' => [
                'autoclose' => true
            ]
        ])
    ?>
    <?= $form->field($model, 'duracion')->textInput(['placeholder' => "Introduzca la duración del show..."])->label('Duracion en <span id="tipo_duracion"></span>') ?>
    <?= $form->field($model, 'listaGeneros')
        ->widget(\kartik\select2\Select2::class, [
            'data' => $listaGeneros,
            'options' => [
                'placeholder' => 'Seleccione los generos de este show...',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => true,
            ],
        ])
    ?>
    <?= $form->field($model, 'gestor_id')
        ->widget(\kartik\select2\Select2::class, [
            'data' => $listaGestores,
            'options' => [
                'hidden' => true,
                'placeholder' => 'Selecciona el gestor de subida donde almacenar la imagen...',
            ],
        ])
    ?>
    <?= $form->field($model, 'imgUpload')->widget(\kartik\file\FileInput::class, [
        'options' => ['accept' => 'image/*'],
        ]);
    ?>
    <?= $form->field($model, 'trailer_link')->textInput(['placeholder' => "Introduzca el enlace a el trailer..."]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
