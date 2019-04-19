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

    tableParticipantes = $('tbody#custom-table-participantes');
    inputParticipantes = $('input#listaParticipantes');
    rolId = $('select#input-roles');
    rolNombre = $('span#select2-input-roles-container');
    rolError = $('div#empty-rol');
    personaId = $('select#input-personas');
    personaNombre = $('span#select2-input-personas-container');
    personaError = $('div#empty-persona');
    registroDuplicado = $('div#registro-duplicado');
    participantes = [];
    
    $('button#custom-button-add').on('click', (e) => {
        e.preventDefault();
        
        if(!hasErrors()) {
            if(participantes[rolId.val()] !== undefined) {
                participantes[rolId.val()].push(personaId.val());
            } else {
                participantes[rolId.val()] = [];
                participantes[rolId.val()].push(personaId.val());
            }
            
            inputParticipantes.val(JSON.stringify(participantes));
            
            addRow(rolId.val(), rolNombre.html(), personaId.val(), personaNombre.html());
        }
    });
    
    function addRow(rolId, rol, personaId, nombre)
    {
        $('<tr>')
        .attr('id', 'custom-row-' + rolId + '-' + personaId)
        .append(
            $('<td>')
            .html(nombre)
        )
        .append(
            $('<td>')
            .html(rol)
        )
        .append(
            $('<td>')
            .append(
                $('<span>')
                .data({
                    rol: rolId,
                    persona: personaId
                })
                .addClass('glyphicon glyphicon-trash')
                .on('click', (e) => {
                    e.preventDefault();
                    target = $(e.target);
                    participantes[target.data('rol')].splice(participantes[target.data('rol')].indexOf(target.data('persona')), 1);
                    inputParticipantes.val(JSON.stringify(participantes));
                    $(e.target).parents('tr').remove();
                })
            )
        )
        .appendTo(tableParticipantes);
    }

    function hasErrors()
    {
        error = false;
        
        if(rolId.val() == '') {
            rolError.show();
            error = true;
        }
        
        if(personaId.val() == '') {
            personaError.show();
            error = true;
        }
        
        if(!error) {
            if(tableParticipantes.children('#custom-row-' + rolId.val() + '-' + personaId.val()).length !== 0) {
                registroDuplicado.show();
                error = true;
            }
        }
        
        return error; 
    }
    
    if(inputParticipantes.val() !== '') {
        lista = JSON.parse(inputParticipantes.val());
        $.each(lista, (i, value) => {
            if (Array.isArray(value) && value.length) {
                $.each(value, (j, value) => {
                    addRow(i, rolId.children(i).html(), value, personaId.children(value).html());
                });
            }
        });
    }
EOJS;

$css = <<<EOCSS
    .custom-error {
        color: #E84747;
        display: none;
    }
EOCSS;

$this->registerCss($css);
?>

<div class="shows-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
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
            ->label('Duracion en <span id="tipo_duracion">...</span>') .
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
            ]) .
        $form->field($model, 'imgUpload')->widget(FileInput::class, [
            'options' => ['accept' => 'image/*'],
            'pluginOptions' => [
                'showUpload' => false
            ]
        ]) .
        $form->field($model, 'trailer')->textInput(['placeholder' => "Introduzca el enlace a el trailer..."])
    );

    $items[] = Utility::tabXOption('Uploads',
        $form->field($model, 'gestorId')
            ->widget(\kartik\select2\Select2::class, [
                'data' => $listaGestores,
                'options' => [
                    'placeholder' => 'Selecciona un gestor de subida...',
                ]
            ]) .
        $form->field($model, 'showUpload')->widget(FileInput::class, [
            'options' => ['accept' => 'video/*'],
            'pluginOptions' => [
                'showUpload' => false
            ]
        ])
    );

    $items[] = Utility::tabXOption('Participantes',
        '<label class="control-label">Personas</label>' .
        Select2::widget([
            'name' => 'persona',
            'id' => 'input-personas',
            'data' => $listaPersonas,
            'options' => [
                'placeholder' => 'Select provinces ...',
            ],
            'pluginEvents' => [
                "change" => "function() {
                    personaError.hide();
                    registroDuplicado.hide();
                }"
            ]
        ]) .
        '<div id="empty-persona" class="help-block custom-error">Personas no puede estar vacio.</div>' .
        $form->field($model, 'listaParticipantes')->hiddenInput(['id' => 'listaParticipantes'])->label(false) .
        '<label class="control-label">Roles</label>' .
        Select2::widget([
            'name' => 'rol',
            'id' => 'input-roles',
            'data' => $listaRoles,
            'options' => [
                'placeholder' => 'Select provinces ...',
            ],
            'pluginEvents' => [
                "change" => "function() {
                    rolError.hide();
                    registroDuplicado.hide();
                }"
            ]
        ]) .
        '<div id="empty-rol" class="help-block custom-error">Roles no puede estar vacio.</div>' .
        '<div id="registro-duplicado" class="help-block custom-error">No pueden existir registros duplicados.</div>' .
        '<button id="custom-button-add" type="button" class="btn btn-success" style="margin-top: 15px">Añadir</button>
        <table class="table table-bordered table-striped" style="margin-top: 20px">
            <thead>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Acciones</th>            
            </thead>
            <tbody id="custom-table-participantes">
            </tbody>
        </table>'
    );

    echo TabsX::widget([
            'items' => $items,
            'position' => TabsX::POS_ABOVE,
            'bordered' => true,
            'encodeLabels' => false
        ]) . '<br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['id' => 'botonGuardar', 'class' => 'btn btn-success']) ?>
    </div>

    <?php
    ActiveForm::end();

    $this->registerJs($js);
    ?>

</div>
