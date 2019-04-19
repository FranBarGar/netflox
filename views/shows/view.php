<?php

use app\helpers\Utility;
use kartik\tabs\TabsX;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Shows */

$formatter = Yii::$app->formatter;
$this->title = $model->titulo;
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$css = <<<EOCSS
    div.rating-container {
        display: inline-block;
        padding-left: 10px;
    }
EOCSS;

$this->registerCss($css);
?>

<div class="shows-view media">

    <?php
    if ($model->tieneImagen()) {
        echo '<div class="media-left media-top text-center">';
        echo Html::img($model->imagen->link, ['alt' => 'Enlace roto', 'width' => '200px', 'class' => 'media-object']);

        echo '<label class="control-label">Rating</label>';
//            echo \kartik\rating\StarRating::widget([
//                    'model' => $model, 'attribute' => 'getValoracionMedia',
//                    'pluginOptions' => [
//                        'theme' => 'krajee-uni',
//                        'filledStar' => '&#x2605;',
//                        'emptyStar' => '&#x2606;'
//
//                    ]
//                ]);

        echo \kartik\rating\StarRating::widget([
            'name' => 'my_rating_' . $model->id,
            'value' => 2.8,
            'pluginOptions' => [
                'readonly' => false,
                'showClear' => false,
                'showCaption' => true,
            ],
        ]);
        echo '</div>';
    }
    ?>

    <div class="media-body">
        <h1 class="media-heading">
            <?= Html::encode($model->titulo) .
//            TODO: Que coja la valoracion media.
            \kartik\rating\StarRating::widget([
                'name' => 'rating_20',
                'value' => 5,
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
            $items[] = array_merge($items, Utility::tabXOption('Trailer', "<div class='media-object'>$trailer</div>"));
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

        <?= TabsX::widget([
        'items' => $items,
        'position' => TabsX::POS_ABOVE,
        'bordered' => true,
        'encodeLabels' => false
        ]);
        ?>
        <br>

        <?php
        if (!empty($model->archivos)) :
        ?>
            <li class='list-group-item active'>
                <span class='badge'><?= $model->duracion ?></span>
                Links de descarga
            </li>
            <?= TabsX::widget([
                'items' => Utility::tabXArchivos($model->archivos),
                'position'=>TabsX::POS_LEFT,
                'bordered' => true,
                'encodeLabels' => false
            ]) ?>
            <br>
        <?php
        endif;
        ?>

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
    </div>

</div>
