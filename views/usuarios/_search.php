<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group col-xs-12 col-md-6">
        <?= $form->field($model, 'nick') ?>
    </div>
    <div class="form-group col-xs-12 col-md-6">
        <?= $form->field($model, 'email') ?>
    </div>

    <div class="form-group col-xs-12 col-md-12">
    <?= $form->field($model, 'biografia')->textarea(['rows' => 3]) ?>
    </div>

    <div class="form-group col-xs-12 col-md-12">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
