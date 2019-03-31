<?php

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

    <?= $form->field($model, 'tipo_id')
        ->widget(\kartik\select2\Select2::class,['data'=>$listaTipos,
            'options'=>[
                'placeholder'=> 'Selecciona un tipo de show...',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
