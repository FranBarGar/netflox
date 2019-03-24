<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShowsGeneros */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shows-generos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'show_id')->textInput() ?>

    <?= $form->field($model, 'genero_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
