<?php

use app\helpers\Utility;
use app\models\Comentarios;
use kartik\tabs\TabsX;
use kartik\widgets\StarRating;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */

$this->title = $model->titulo;
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$formatter = Yii::$app->formatter;

$css = <<<EOCSS
    div.rating-container {
        display: inline-block;
        padding-left: 10px;
    }
    .all-comments {
        background-color: #fff5ed;
    }
    .comentario {
        padding: 5px;
    }
    .comentario-cuerpo {
        position: relative;
        padding: 5px 10px 5px 30px;
        border-radius: 2px;
    }
    .comentario-texto {
        padding-left: 10px;
    }
    .votos {
        padding-left: 10px;
        padding-top: 3px;
    }
EOCSS;


$this->registerCss($css);
?>

<div class="shows-view media">

    <div class="media-left media-top text-center">
        <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'width' => '200px', 'class' => 'media-object']) ?>

        <label class="control-label">Tu valoración</label>

        <?=
        StarRating::widget([
            'name' => 'my_rating_' . $model->id,
            'value' => $valoracion->valoracion ?: 0,
            'pluginOptions' => [
                'readonly' => true,
                'showClear' => false,
                'showCaption' => true,
            ],
        ])
        ?>

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
            <?= Html::encode($model->titulo) .
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

        <?php
        $items = [];
        if (!empty($model->sinopsis)) {
            $items[] = Utility::tabXOption('Sinopsis', "<p>$model->sinopsis</p>");
        }

        if ($model->trailer !== null && ($trailer = \Embed\Embed::create($model->trailer)->getCode()) != '') {
            $src = explode('"', explode('src="', $trailer)[1])[0];

            $items[] = array_merge($items, Utility::tabXOption('Trailer', "
                <div class='embed-responsive embed-responsive-16by9'>
                    <iframe class='embed-responsive-item' src='$src'></iframe>
                </div>
            "));
        }

        // Participantes
        if (!empty($model->participantes)) {
            $string = '<ul>';
            $fixParticipantes = Utility::fixParticipantes($model->participantes);
            foreach ($fixParticipantes as $rol => $personas) {
                $string .= "<li>$rol: <ul>";
                foreach ($personas as $nombre) {
                    $string .= "<li>$nombre</li>";
                }
                $string .= '</li></ul>';
            }
            $string .= '</ul>';
            $items[] = Utility::tabXOption('Participantes', $string);
        }
        ?>

        <?=
        TabsX::widget([
            'items' => $items,
            'position' => TabsX::POS_ABOVE,
            'bordered' => true,
            'encodeLabels' => false
        ]);
        ?>
        <br>

        <?php if (!empty($model->archivos)) : ?>
            <li class='list-group-item active'>
                <span class='badge'><?= $model->duracion ?></span>
                Links de descarga
            </li>
            <?=
            TabsX::widget([
                'items' => Utility::tabXArchivos($model->archivos),
                'position' => TabsX::POS_LEFT,
                'bordered' => true,
                'encodeLabels' => false
            ])
            ?>
            <br>
        <?php endif; ?>

        <?php if (($numHijos = $dataProvider->getCount()) >= 1): ?>
            <ul class="list-group">
                <li class="list-group-item active">
                    <span class="badge">
                        <?= $numHijos . '/' . $model->duracion ?>
                    </span>
                    Lista de <?= $model->tipo->duracion->tipo ?>
                </li>
                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'summary' => '',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('_reducedView.php', ['model' => $model]);
                    },
                ]) ?>
            </ul>
        <?php endif; ?>

        <div class="all-comments">
            <div class="row col-md-offset-5">
                <?php
                Modal::begin([
                    'header' => '<h2>Valorar</h2>',
                    'toggleButton' => [
                        'label' => 'Valorar',
                        'class' => 'btn btn-primary',
                    ],
                ]);

                $action = $valoracion->valoracion == null
                    ? Url::to(['comentarios/create'])
                    : Url::to([
                            'comentarios/update',
                            'id' => $valoracion->id,
                    ]);

                echo $this->render('../comentarios/_valorar', [
                    'model' => $valoracion,
                    'action' => $action
                ]);

                Modal::end();
                ?>
            </div>
            <?php
            pintarComentarios($model->getValoraciones()->all(), $this);


            /**
             * Pinta los comentarios anidados.
             * @param $comentarios
             * @param $vista
             */
            function pintarComentarios($comentarios, $vista)
            {
                ?>
                <?php if ($comentarios) : ?>
                <ul>
                    <?php foreach ($comentarios as $comentario) : ?>
                        <li>
                            <?= $vista->render('../comentarios/view', [
                                'model' => $comentario
                            ]) ?>
                            <?php pintarComentarios($comentario->comentarios, $vista) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif;
            }

            ?>
        </div>
    </div>
</div>
