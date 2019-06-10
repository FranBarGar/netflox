<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\StarRating;

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
        border-radius: 5px;
    }
EOCSS;

$this->registerCss($css);
?>

<div itemscope itemtype="http://schema.org/Movie" class="shows-view media">

    <div class="col-xs-4 col-md-2">
        <div>
            <div id="accion-icono" style="position: absolute;top: 0;right: 18px;">
                <span class="<?= $model->getMiAccion() ?>" style="color: white"></span>
            </div>
            <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'width' => '200px', 'class' => 'img-responsive',  'itemprop' => "image"]) ?>
        </div>
    </div>


    <div class="row col-xs-8 col-md-10">
        <h1 itemprop="name" class="media-heading">
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
                <span itemprop="duration">Duracion: <?= $model->duracion . ' ' . $model->tipo->tipo_duracion ?></span> ---
                <span itemprop="datePublished">Estreno: <?= $formatter->asDate($model->lanzamiento, 'long') ?></span> ---
                <span itemprop="genre">Generos:
                <?php
                if ($generos = $model->obtenerGeneros()) {
                    echo array_shift($generos)->genero;
                    foreach ($generos as $genero) {
                        echo ', ' . $genero->genero;
                    }
                }
                ?>
                </span>
            </p>
        </div>
        <p itemprop="description">
        <?=
        Html::encode($model->sinopsis)
        ?>
        </p>
    </div>
</div>
