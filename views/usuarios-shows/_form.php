<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosShows */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-shows-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'usuario_id')->textInput() ?>

    <?= $form->field($model, 'show_id')->textInput() ?>

    <?= $form->field($model, 'plan_to_watch')->textInput() ?>

    <?= $form->field($model, 'droppped')->textInput() ?>

    <?= $form->field($model, 'watched')->textInput() ?>

    <?= $form->field($model, 'watching')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
