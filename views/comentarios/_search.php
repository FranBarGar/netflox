<?php

use app\helpers\Utility;
use app\models\Comentarios;
use kartik\widgets\Select2;
use kartik\widgets\StarRating;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosSearch */
/* @var $form yii\widgets\ActiveForm */

$js = <<<EOJS
    $('#krajee-sucks').val($('#comentariossearch-valoracion').val());
    $('#krajee-sucks').rating('create');
    $('#krajee-sucks').on('change', (e) => {
       $('#comentariossearch-valoracion').val(e.target.value);
    });
    $('#search-switch-button').on('click', (e) => {
        $('#search-maximo').toggle(750);
    });
EOJS;

$this->registerJs($js);
?>

<div class="row all-comments comentarios-order">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group col-md-6">
        <?= $form->field($model, 'literalTitulo')->label('Show') ?>
    </div>

    <div class="form-group col-md-6">
        <?= $form->field($model, 'literalUsuario')->label('Usuario') ?>
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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group col-md-6">
                    <?= $form->field($model, 'cuerpo')->textarea(['rows' => 5]) ?>
                </div>

                <div class="form-group col-md-6">
                    <label for="krajee-sucks">Valoracion</label>
                    <input type="text" id="krajee-sucks" class="form-control">
                    <?=
                    $form->field($model, 'valoracion')->hiddenInput()->label(false);
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            <?=
            $form->field($model, 'orderBy')
                ->widget(Select2::class, [
                    'data' => Comentarios::ORDER_BY,
                    'options' => [
                        'placeholder' => 'Seleccione el tipo de ordenación...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
            ?>
        </div>
        <div class="form-group col-md-6">
            <?=
            $form->field($model, 'orderType')
                ->widget(Select2::class, [
                    'data' => Utility::ORDER_TYPE,
                    'options' => [
                        'placeholder' => 'Selecciona un tipo de show a buscar...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
