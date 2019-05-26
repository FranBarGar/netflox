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
?>

<div class="row all-comments comentarios-order">

    <div class="col-md-12">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($model, 'cuerpo') ?>
        <?= $form->field($model, 'usuario_id') ?>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group col-md-6">
                <?=
//                $form->field($model, 'valoracion')->widget(StarRating::classname());

                $form->field($model, 'valoracion');
                ?>
            </div>

            <div class="form-group col-md-6">
                <?= $form->field($model, 'show_id') ?>
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

    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
