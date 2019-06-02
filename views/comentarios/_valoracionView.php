<?php

use kartik\widgets\StarRating;
use yii\helpers\Html;
use \yii\bootstrap\Modal;
use yii\helpers\Url;

\kartik\rating\StarRatingAsset::register($this);

?>
<div class="col-md-12 col-xs-12 comentario comentario-margin border-bottom-custom">
    <small>
        <div class="col-md-12 col-xs-12">
            <h4><?= Html::a($model->show->getFullTittle(), ['shows/view', 'id' => $model->show->id]) ?></h4>
        </div>
        <div class="col-md-8 col-xs-8">
            Creado
            por <?= Html::a(Html::encode($model->usuario->nick), ['usuarios/view', 'id' => $model->usuario->id]) ?>
            el <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
        </div>
        <div class="col-md-4 col-xs-4 text-right">
            <?php
            if ($model->edited_at !== null) {
                echo 'Ultima edicion: ' . Yii::$app->formatter->asDatetime($model->edited_at);
            }
            ?>
        </div>
    </small>
    <div class="col-md-12 col-xs-12 comentario-margin">
        <p>
            <?= Html::encode($model->cuerpo) ?>
        </p>
    </div>
    <div class="col-md-12 col-xs-12">
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
    <?php
    if ($model->votoUsuario == 1) {
        $model->likes = '<b>' . $model->likes . '</b>';
    } elseif ($model->votoUsuario == -1) {
        $model->dislikes = '<b>' . $model->dislikes . '</b>';
    }
    ?>
    <div class="col-md-4 col-xs-5">
        <?=
        Html::button(
            'Like (<span id="num-like-' . $model->id . '">' . $model->likes . '</span>)',
            [
                'class' => 'voto btn btn-primary btn-xs',
                'id' => 'like-' . $model->id,
                'data-voto-id' => $model->id,
                'data-voto' => 1
            ]
        )
        .
        Html::button(
            'Dislike (<span id="num-dislike-' . $model->id . '">' . $model->dislikes . '</span>)',
            [
                'class' => 'voto btn btn-danger btn-xs',
                'id' => 'dislike-' . $model->id,
                'data-voto-id' => $model->id,
                'data-voto' => -1
            ]
        )
        ?>
    </div>


    <div class="col-md-8 col-xs-7 text-right">
        <?php

        if (($duenyo = $model->usuario_id == Yii::$app->user->id) || Yii::$app->user->identity->rol == 'admin') {
            echo Html::a('Eliminar', ['comentarios/delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => '¿Estas seguro de eliminar este comentario? Esto eliminara todos sus comentarios anidados.',
                    'method' => 'post',
                ],
            ]);
        }

        if ($duenyo) {
            if ($model->valoracion == null) {
                Modal::begin([
                    'header' => '<h2 class="text-left">Editar comentario.</h2>',
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
        ?>
    </div>
</div>
<hr>