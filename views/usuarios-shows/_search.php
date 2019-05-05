<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosShowsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-shows-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'usuario_id') ?>

    <?= $form->field($model, 'show_id') ?>

    <?= $form->field($model, 'plan_to_watch') ?>

    <?= $form->field($model, 'droppped') ?>

    <?php // echo $form->field($model, 'watched') ?>

    <?php // echo $form->field($model, 'watching') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
