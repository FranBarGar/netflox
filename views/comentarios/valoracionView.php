<?php

use kartik\widgets\StarRating;
use yii\helpers\Html;
use \yii\bootstrap\Modal;
use yii\helpers\Url;

?>

<div class="row comentario">
    <div class="col-md-12 comentario-info">
        <small>
            <div class="col-md-8">
                Creado
                por <?= Html::a(Html::encode($model->usuario->nick), ['usuarios/view', 'id' => $model->usuario->id]) ?>
                el <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
            </div>
            <div class="col-md-4">
                <?php
                if ($model->edited_at !== null) {
                    echo 'Ultima edicion: ' . Yii::$app->formatter->asDatetime($model->edited_at);
                }
                ?>
            </div>
        </small>
    </div>
    <div class="col-md-12 comentario-texto">
        <p>
            <?= Html::encode($model->cuerpo) ?>
        </p>
    </div>
    <?php if ($model->valoracion !== null) : ?>
        <div class="col-md-12">
            <?=
            StarRating::widget([
                'name' => 'user_rating' . $model->id,
                'value' => $model->valoracion,
                'pluginOptions' => [
                    'size' => 'sm',
                    'readonly' => true,
                    'showClear' => false,
                    'showCaption' => false,
                ],
            ])
            ?>
        </div>
    <?php endif; ?>
</div>