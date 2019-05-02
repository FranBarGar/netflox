<?php

use kartik\widgets\StarRating;
use yii\helpers\Html;
use \yii\bootstrap\Modal;
use yii\helpers\Url;

?>

<div class="row comentario">
    <div class="row comentario-info">
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
    <div class="row comentario-texto">
        <p>
            <?= Html::encode($model->cuerpo) ?>
        </p>
    </div>
    <?php if ($model->valoracion !== null) : ?>
        <div class="row">
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
    <div class="actions row">
        <?= Html::label('Likes: '.$model->votosTotales, ['id' => 'votos-totales-' . $model->id]) ?>
        <?php if ($model->votoUsuario === 1) {
            echo Html::button('Dislike', ['class' => 'voto btn btn-danger btn-xs', 'id' => 'voto-' . $model->id, 'data-voto-id' => $model->id]);
        } elseif ($model->votoUsuario === -1) {
            echo Html::button('Like', ['class' => 'voto btn btn-primary btn-xs', 'id' => 'voto-' . $model->id, 'data-voto-id' => $model->id]);
        } else {
            echo Html::button('Like', ['class' => 'voto btn btn-primary btn-xs', 'id' => 'voto-' . $model->id, 'data-voto-id' => $model->id]);
            echo Html::button('Dislike', ['class' => 'voto btn btn-danger btn-xs', 'id' => 'voto-' . $model->id, 'data-voto-id' => $model->id]);
        }

        if ($model->usuario_id == Yii::$app->user->id) {
            echo Html::a('Eliminar', ['comentarios/delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-xs',
                    'data' => [
                        'confirm' => '¿Estas seguro de eliminar este comentario? Esto eliminara todos sus comentarios anidados.',
                        'method' => 'post',
                    ],
                ]) . ' ';

            if ($model->valoracion == null) {
                Modal::begin([
                    'header' => '<h2>Editar comentario.</h2>',
                    'toggleButton' => [
                        'label' => 'Editar',
                        'class' => 'btn btn-primary btn-xs',
                    ],
                ]);

                echo $this->render('../comentarios/_comentar', [
                    'model' => $model,
                    'action' => Url::to([
                        'comentarios/update',
                        'id' => $model->id,
                    ])
                ]);

                Modal::end();
            }
        }

        Modal::begin([
            'header' => '<h2>Responder al comentario.</h2>',
            'toggleButton' => [
                'label' => 'Responder',
                'class' => 'btn btn-primary btn-xs',
            ],
        ]);

        echo $this->render('../comentarios/_comentar', [
            'model' => $comentarioHijo,
            'action' => Url::to(['comentarios/create'])
        ]);
        Modal::end();
        ?>
    </div>
</div>