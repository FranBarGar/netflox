<?php

use app\helpers\Utility;
use kartik\datecontrol\DateControl;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\tabs\TabsX;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */
/* @var $form yii\widgets\ActiveForm */

$url = \yii\helpers\Url::to(['shows/ajax-create-info']);
$urlAddParticipante = \yii\helpers\Url::to(['participantes/ajax-create']);
$urlDeleteParticipante = \yii\helpers\Url::to(['participantes/delete']);
$urlAddArchivos = \yii\helpers\Url::to(['archivos/ajax-create']);
$urlDeleteArchivos = \yii\helpers\Url::to(['archivos/delete']);
$show_id = $model->id;
$js = <<<EOJS
    div = $('div.field-shows-show_id');
    select = div.children('select');
    divShowId = $('div.form-group.field-shows-show_id');
    showId = $("select#shows-show_id");
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
            data: { 
                id: tipoId,
                show_id: $show_id
                },
            success: function (data) {
                data = JSON.parse(data);
                padres = data[1];
                if (padres === false) {
                    showId.prop('required', false);
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

    gridParticipantes = $('#grid-view-participantes');
    rolId = $('select#input-roles');
    rolError = $('div#empty-rol');
    personaId = $('select#input-personas');
    personaError = $('div#empty-persona');
    registroDuplicado = $('div#registro-duplicado');
    
    $('button#custom-button-add').on('click', (e) => {
        e.preventDefault();
        
        if(!participantesHasErrors()) {
            $.post({
            url: '$urlAddParticipante',
            data: { 
                persona_id: personaId.val(),
                rol_id: rolId.val(),
                show_id: $show_id
                },
            success: function (data) {
                data = JSON.parse(data);
                if(data == '') {
                    registroDuplicado.show();
                } else {
                    gridParticipantes.html(data);
                }
            }
            });
        }
    });
    
    gridParticipantes.on('click', '.delete', (e) => {
        e.preventDefault();
        
        $.post({
            url: '$urlDeleteParticipante',
            data: { 
                id: e.target.id
            },
            success: function (data) {
                data = JSON.parse(data);
                gridParticipantes.html(data);
            }
        });
    });
    
    gridArchivos = $('#grid-view-archivos');
    descripcion = $('input#archivo-descripcion');
    descripcionError = $('div#empty-descripcion');
    link = $('input#archivo-link');
    linkError = $('div#empty-link');
    registroDuplicadoArchivos = $('div#registro-duplicado-archivos');
    
    $('button#custom-button-add-archivos').on('click', (e) => {
        e.preventDefault();
        
        if(!archivosHasErrors()) {
            $.post({
            url: '$urlAddArchivos',
            data: { 
                link: link.val(),
                descripcion: descripcion.val(),
                show_id: $show_id
                },
            success: function (data) {
                data = JSON.parse(data);
                if(data == '') {
                    registroDuplicadoArchivos.show();
                } else {
                    gridArchivos.html(data);
                }
            }
            });
        }
    });
    
    gridArchivos.on('click', '.archivos-delete', (e) => {
        e.preventDefault();
        
        $.post({
            url: '$urlDeleteArchivos',
            data: { 
                id: e.target.id
            },
            success: function (data) {
                data = JSON.parse(data);
                gridArchivos.html(data);
            }
        });
    });
    

    function archivosHasErrors()
    {
        error = false;
        
        if(link.val() == '') {
            linkError.show();
            error = true;
        } else {
            linkError.hide();
        }
        
        if(descripcion.val() == '') {
            descripcionError.show();
            error = true;
        } else {
            descripcionError.hide();
        }
        
        return error; 
    }

    function participantesHasErrors()
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
        
        return error; 
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
        'options' => ['enctype' => 'multipart/form-data'],
    ]);

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
        $form->field($model, 'show_id', ['enableAjaxValidation' => true])
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
            ->label('Duracion en <span id="tipo_duracion">' . $model->tipo->tipo_duracion . '</span>') .
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
                'showUpload' => false,
                'initialPreview' => [
                    $model->getImagenLink()
                ],
                'initialPreviewAsData' => true,
                'initialCaption' => Html::encode($model->titulo),
                'initialPreviewConfig' => [
                    ['caption' => Html::encode($model->titulo)],
                ],
                'overwriteInitial' => true,
                'maxFileSize' => 5000
            ]
        ]) .
        $form->field($model, 'trailer')->textInput(['placeholder' => "Introduzca el enlace a el trailer..."])
    );

    $items[] = Utility::tabXOption('Uploads',
        $form->field($model, 'showUpload')->widget(FileInput::class, [
            'options' => ['accept' => 'video/*'],
            'pluginOptions' => [
                'showUpload' => false
            ]
        ]) .
        '<div class="form-group">
        <div class="col-md-5 col-sm-12" style="margin-bottom: 20px">
        <label class="control-label">Descripcion</label>
        <input id="archivo-descripcion" class="form-control" value="">
        <div id="empty-descripcion" class="help-block custom-error">Descripcion no puede estar vacio.</div>
        </div>
        <div class="col-md-5 col-sm-12">
        <label class="control-label">Link</label>
        <input id="archivo-link" class="form-control" value="">
        <div id="empty-link" class="help-block custom-error">Link no puede estar vacio.</div>
        </div>
        <button id="custom-button-add-archivos" type="button" class="btn btn-success col-md-2" style="margin-top: 25px">Añadir</button>
        </div>
        <div id="registro-duplicado-archivos" class="help-block custom-error">No pueden existir links duplicados.</div>
        <div id="grid-view-archivos">' .
        GridView::widget([
            'summary' => '',
            'dataProvider' => $archivosProvider,
            'columns' => [
                'descripcion',
                'link',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            return '<span id="'.$model->id.'" class="glyphicon glyphicon-trash archivos-delete"></span>';
                        }

                    ],
                ],
            ],
        ]) .
        '</div>'
    );

    $items[] = Utility::tabXOption('Participantes',
        '<div class="form-group">
        <div class="col-md-5 col-sm-12" style="margin-bottom: 20px">
        <label class="control-label">Personas</label>' .
        Select2::widget([
            'name' => 'persona',
            'id' => 'input-personas',
            'data' => $listaPersonas,
            'options' => [
                'placeholder' => 'Seleccione el participante...',
            ],
            'pluginEvents' => [
                "change" => "function() {
                    personaError.hide();
                    registroDuplicado.hide();
                }"
            ]
        ]) .
        '<div id="empty-persona" class="help-block custom-error">Personas no puede estar vacio.</div>
        </div>
        <div class="col-md-5 col-sm-12">
        <label class="control-label">Roles</label>' .
        Select2::widget([
            'name' => 'rol',
            'id' => 'input-roles',
            'data' => $listaRoles,
            'options' => [
                'placeholder' => 'Selecciona el rol del participante...',
            ],
            'pluginEvents' => [
                "change" => "function() {
                    rolError.hide();
                    registroDuplicado.hide();
                }"
            ]
        ]) .
        '<div id="empty-rol" class="help-block custom-error">Roles no puede estar vacio.</div>
        </div>
        <button id="custom-button-add" type="button" class="btn btn-success col-md-2" style="margin-top: 25px">Añadir</button>
        </div>
        <div id="registro-duplicado" class="help-block custom-error">No pueden existir registros duplicados.</div>
        <div id="grid-view-participantes">' .
        GridView::widget([
            'summary' => '',
            'dataProvider' => $participantesProvider,
            'columns' => [
                'persona.nombre',
                'rol.rol',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            return '<span id="'.$model->id.'" class="glyphicon glyphicon-trash delete"></span>';
                        }
                    ],
                ],
            ],
        ]) .
        '</div>'
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
