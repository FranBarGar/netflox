<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShowsDescargas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shows-descargas-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'num_descargas')->textInput() ?>

    <?= $form->field($model, 'archivo_id')->textInput() ?>

    <?= $form->field($model, 'show_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
