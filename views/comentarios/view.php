<?php

use app\models\Comentarios;
use kartik\widgets\StarRating;
use yii\helpers\Html;
use \yii\bootstrap\Modal;
use yii\helpers\Url;

?>

<div class="comentario">
    <div class="row comentario-cuerpo">
        <div class="row comentario-info">
            <div class="col-md-8">
                <small>
                    Creado por <?= Html::a($model->usuario->nick, ['usuarios/view', 'id' => $model->usuario->id]) ?>
                    el <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                </small>
            </div>
            <div class="col-md-4">
                <small>
                    <?php
                    if ($model->usuario_id == Yii::$app->user->id) {


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

                        echo Html::a('Eliminar', ['comentarios/delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-xs',
                            'data' => [
                                'confirm' => '¿Estas seguro de eliminar este comentario? Esto eliminara todos sus comentarios anidados.',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    ?>
                </small>
            </div>
        </div>
        <div class="row comentario-texto">
            <?= Html::encode($model->cuerpo) ?>
        </div>
        <div class="row">
            <?php if ($model->valoracion !== null) : ?>
            <div class="col-md-4">
                <?= StarRating::widget([
                    'name' => 'user_rating' . $model->id,
                    'value' => $model->valoracion,
                    'pluginOptions' => [
                        'size' => 'sm',
                        'readonly' => true,
                        'showClear' => false,
                        'showCaption' => true,
                    ],
                ]) ?>
                <?php
                $col = 'contestar col-md-offset-6 col-md-2';
                else:
                    $col = 'contestar col-md-offset-10 col-md-2';
                endif;
                ?>
            </div>
            <div class="<?= $col ?>">
                <?php
                Modal::begin([
                    'header' => '<h2>Responder al comentario.</h2>',
                    'toggleButton' => [
                        'label' => 'Responder',
                        'class' => 'btn btn-primary btn-xs',
                    ],
                ]);
                $comentarioHijo = new Comentarios();
                $comentarioHijo->show_id = $model->show_id;
                $comentarioHijo->padre_id = $model->id;
                $comentarioHijo->usuario_id = Yii::$app->user->id;
                echo $this->render('../comentarios/_comentar', [
                    'model' => $comentarioHijo,
                    'action' => Url::to(['comentarios/create'])
                ]);
                Modal::end();
                ?>
            </div>
        </div>
    </div>