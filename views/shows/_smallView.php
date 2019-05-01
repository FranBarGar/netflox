<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\StarRating;

//$url = Url::to(['noticias/menear']);
//$js = <<<EOT
//    $('.boton').click(function (event) {
//        var el = $(this);
//        var id = el.data('key');
//        $.ajax({
//            url: '$url',
//            data: { id: id },
//            success: function (data) {
//                $('#boton-' + id).text(`Mover (` + data +` movimientos)`);
//                el.attr('disabled', true);
//            }
//        });
//    });
//EOT;
//$this->registerJs($js);

$formatter = Yii::$app->formatter;

$css = <<<EOCSS
    div.rating-container {
        display: inline-block;
        padding-left: 10px;
        padding-top: 5px;
        vertical-align: top;
    }
    
    div.shows-smallView {
        padding: 4px;
        margin-bottom: 0px;
        border: 1px solid gray;
        border-radius: 2px;
        height: 250px;
    }
    
    div.media-left.media-middle {
        object-fit: cover;
    }
EOCSS;

$this->registerCss($css);
?>

<div class="shows-view media">

    <div class="media-left media-top text-center">
        <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'width' => '200px', 'class' => 'media-object']) ?>

        <?php if (Yii::$app->user->identity->rol == 'admin') : ?>
            <?= Html::a('Actualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a('Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '¿Estas seguro de eliminar este show? Esto eliminara todos sus contenidos asociados como comentarios, "hijos", etc...',
                    'method' => 'post',
                ],
            ])
            ?>
        <?php endif; ?>
    </div>


    <div class="media-body">
        <h1 class="media-heading">
            <?= Html::a(Html::encode($model->titulo), [
                'shows/view',
                'id' => $model->id,
            ]) .
            StarRating::widget([
                'name' => 'rating_20',
                'value' => $model->valoracionMedia,
                'pluginOptions' => [
                    'size' => 'sm',
                    'stars' => 1,
                    'min' => 0,
                    'max' => 5,
                    'displayOnly' => true,
                ],
            ]); ?>
        </h1>
        <div class="info">
            <p>
                Duracion: <?= $model->duracion . ' ' . $model->tipo->duracion->tipo ?> ---
                Estreno: <?= $formatter->asDate($model->lanzamiento, 'long') ?> ---
                Generos:
                <?php
                if ($generos = $model->tieneGeneros()) {
                    echo array_shift($generos)->genero;
                    foreach ($generos as $genero) {
                        echo ', ' . $genero->genero;
                    }
                }
                ?>
            </p>
        </div>

        <?=
        $model->sinopsis
        ?>
    </div>
</div>
