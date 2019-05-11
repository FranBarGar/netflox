<?php

use kartik\rating\StarRating;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Comentarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comentarios-form">

    <?php $form = ActiveForm::begin([
            'action' =>  $model->valoracion == null
            ? Url::to(['comentarios/valorar'])
            : Url::to([
                'comentarios/valorar-update',
                'id' => $model->id,
            ])
    ]); ?>

    <?= $form->field($model, 'cuerpo')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'padre_id')->hiddenInput(['value' => $model->padre_id])->label(false) ?>
    <?= $form->field($model, 'show_id')->hiddenInput(['value' => $model->show_id])->label(false) ?>
    <?= $form->field($model, 'usuario_id')->hiddenInput(['value' => $model->usuario_id])->label(false)  ?>
    <?= $form->field($model, 'valoracion')->widget(StarRating::class, [
        'pluginOptions' => ['step' => 0.5]
    ]);  ?>

    <div class="form-group">
        <?= Html::submitButton('Valorar', ['class' => 'btn btn-success']) ?>
        <?php
        if ($model->id !== null) {
            echo Html::a('Eliminar', ['comentarios/delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '¿Estas seguro de eliminar este comentario? Esto eliminara todos sus comentarios anidados.',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
