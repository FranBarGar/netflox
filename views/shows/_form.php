<?php

use app\helpers\Utility;
use kartik\datecontrol\DateControl;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\tabs\TabsX;
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

    <?php $form = ActiveForm::begin([
        'options'=>['enctype'=>'multipart/form-data']
    ]); ?>

    <?php
    $items = [];

    $items[] = Utility::tabXOption('General',
        $form->field($model, 'tipo_id')
            ->widget(Select2::class, [
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
            ]) .
        $form->field($model, 'show_id')
            ->widget(Select2::class, [
                'options' => [
                    'hidden' => true,
                    'placeholder' => 'Selecciona el show al que pertenece...',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) .
        $form->field($model, 'titulo')->textInput([
            'maxlength' => true,
            'placeholder' => "Introduzca el titulo del show..."
        ]) .
        $form->field($model, 'sinopsis')->textarea([
            'rows' => 6,
            'placeholder' => "Introduzca la sinopsis del show..."
        ]) .
        $form->field($model, 'lanzamiento')->widget(DateControl::class, [
            'type' => 'date',
            'ajaxConversion' => true,
            'autoWidget' => true,
            'widgetClass' => '',
            'displayFormat' => 'php:d-F-Y',
            'saveFormat' => 'php:Y-m-d',
            'saveTimezone' => 'UTC',
            'displayTimezone' => 'Europe/Madrid',
            'widgetOptions' => [
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'php:d-F-Y'
                ]
            ],
            'language' => 'es'
        ]) .
        $form->field($model, 'duracion')
            ->textInput(['placeholder' => "Introduzca la duración del show..."])
            ->label('Duracion en <span id="tipo_duracion"></span>') .
        $form->field($model, 'listaGeneros')
            ->widget(Select2::class, [
                'data' => $listaGeneros,
                'options' => [
                    'placeholder' => 'Seleccione los generos de este show...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ])
    );

    $items[] = Utility::tabXOption('Uploads',
        $form->field($model, 'imgUpload')->widget(FileInput::class, [
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'showUpload' => false
            ]
        ]) .
        $form->field($model, 'trailer')->textInput(['placeholder' => "Introduzca el enlace a el trailer..."]) .
        $form->field($model, 'gestor_id')
            ->widget(\kartik\select2\Select2::class, [
                'data' => $listaGestores,
                'options' => [
                    'placeholder' => 'Selecciona un gestor de subida...',
                ]
            ]) .
        $form->field($model, 'showUpload')->widget(FileInput::class, [
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'showUpload' => false
            ]
        ])
    );

    echo TabsX::widget([
            'items' => $items,
            'position' => TabsX::POS_ABOVE,
            'bordered' => true,
            'encodeLabels' => false
        ]) . '<br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
