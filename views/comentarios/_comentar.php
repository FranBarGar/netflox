<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Comentarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comentarios-form text-left">

    <?php $form = ActiveForm::begin(['action' => $action]); ?>

    <?= $form->field($model, 'cuerpo')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'padre_id')->hiddenInput(['value' => $model->padre_id])->label(false) ?>
    <?= $form->field($model, 'show_id')->hiddenInput(['value' => $model->show_id])->label(false) ?>
    <?= $form->field($model, 'usuario_id')->hiddenInput(['value' => $model->usuario_id])->label(false)  ?>

    <div class="form-group">
        <?= Html::submitButton('Comentar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
