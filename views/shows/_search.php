<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShowsSearch */
/* @var $form yii\widgets\ActiveForm */

$js = <<<EOJS
    $('#search-switch-button').on('click', (e) => {
        $('#search-maximo').toggle(750);
    });
EOJS;

$this->registerJs($js);
?>

<div class="shows-search row">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div id="search-minimo" class="form-row">
        <div class="form-group col-md-6">
            <?= $form->field($model, 'titulo') ?>
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


    <div id="search-buttons" class="form-group col-md-12">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary col-md-3']) ?>
        <?=
        Html::button(
            '<span class="glyphicon glyphicon-cog"></span>',
            [
                'class' => 'btn btn-danger col-md-1',
                'id' => 'search-switch-button'
            ]
        )
        ?>
    </div>

    <div id="search-maximo" style="display: none">
        <div class="form-row">
            <div class="form-group col-md-6">
                <?=
                $form->field($model, 'tipo_id')
                    ->widget(Select2::class, [
                        'data' => $listaTipos,
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
                $form->field($model, 'accion')
                    ->widget(Select2::class, [
                        'data' => $listaAcciones,
                        'options' => [
                            'placeholder' => 'Selecciona la accion sobre el show a buscar...',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
                ?>
            </div>
        </div>

        <div class="form-row">
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
            <div class="form-group col-md-6">
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
    </div>

    <?php ActiveForm::end(); ?>

</div>
