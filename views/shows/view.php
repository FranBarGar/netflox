<?php

use app\helpers\Utility;
use kartik\tabs\TabsX;
use kartik\widgets\Select2;
use kartik\widgets\StarRating;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

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
        padding-top: 5px;
        vertical-align: top;
    }
EOCSS;

$this->registerCss($css);

$this->registerJs(Utility::AJAX_VOTAR);
$this->registerCss(Utility::CSS . $css);
?>

<div class="row shows-view">

    <div class="col-md-3 text-center align-content-center">

        <div>
            <div id="accion-icono" style="position: absolute;top: 0;right: 18px;">
                <span class="<?= $model->getMiAccion() ?>" style="color: white"></span>
            </div>
            <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'class' => 'img-responsive']) ?>
        </div>

        <?php
        Modal::begin([
            'header' => '<h2>Valorar</h2>',
            'toggleButton' => [
                'label' => 'Valorar',
                'class' => 'btn btn-block btn-primary',
            ],
        ]);

        echo $this->render('../comentarios/_valorar', [
            'model' => $valoracion,
        ]);

        Modal::end();
        ?>

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

        <?php
        echo $this->render('../usuarios-shows/_form.php', [
            'model' => $accion,
            'listaAcciones' => $listaAcciones,
        ]);

        if (Yii::$app->user->identity->rol == 'admin') : ?>
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


    <div class="col-md-9">
        <h1 class="col-md-12 heading">
            <?= Html::encode($model->titulo) ?>
            <?=
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
            ]);
            ?>
        </h1>

        <?php
        $items = [];
        if (!empty($model->sinopsis)) {
            $items[] = Utility::tabXOption('Sinopsis', '<p>' . Html::encode($model->sinopsis) . '</p>');
        }

        $str = '
        <ul>
            <li>General: 
                <ul>
                    <li>
                        Duracion: ' . $model->duracion . ' ' . $model->tipo->tipo_duracion . '
                    </li>
                    <li>
                        Estreno: ' . $formatter->asDate($model->lanzamiento, 'long') . '
                    </li>
                    <li>
                        Generos: ';

        if ($generos = $model->obtenerGeneros()) {
            $str .= array_shift($generos)->genero;
            foreach ($generos as $genero) {
                $str .= ', ' . $genero->genero;
            }
        }

        $str .= '</li></ul></li>';

        // Participantes
        if (!empty($model->participantes)) {

            $fixParticipantes = Utility::fixParticipantes($model->participantes);

            $str .= '<br><li>Participantes: <ul>';
            foreach ($fixParticipantes as $rol => $personas) {
                $str .= "<li>$rol:<ul>";
                foreach ($personas as $nombre) {
                    $str .= "<li>$nombre</li>";
                }
                $str .= '</li></ul>';
            }
            $str .= '</ul></li>';
        }
        $str .= '</ul>';

        $items[] = Utility::tabXOption('Informacion', $str);


        if ($model->trailer !== null && ($trailer = \Embed\Embed::create($model->trailer)->getCode()) != '') {
            $src = explode('"', explode('src="', $trailer)[1])[0];

            $items[] = array_merge($items, Utility::tabXOption('Trailer', "
                <div class='embed-responsive embed-responsive-16by9'>
                    <iframe class='embed-responsive-item' src='$src'></iframe>
                </div>
            "));
        }

        if (!empty($model->archivos)) {
//                TODO: Descargas totales y archivos en la nube.
            $str = '
            <ul class="list-group">
                <li class="list-group-item active">
                    <span class="badge"> Descargas totales: 0</span>
                    Enlaces
                </li>';
            foreach ($model->archivos as $archivo) {
                $str .= '
                <li class="list-group-item">
                    <span class="badge">' . $archivo->num_descargas . '</span>'
                    . Html::a($archivo->descripcion ?: $model->getFullTittle(), [
                        'archivos/file',
                        'id' => $archivo->id,
                    ]) .
                '</li>';
            }
            $str .= '</ul>';

            $items[] = Utility::tabXOption('Descargas', $str);
        }

        if (($numHijos = $dataProvider->getCount()) >= 1) {
            $str = '
            <ul class="list-group">
                <li class="list-group-item active">
                    <span class="badge">' . $numHijos . '/' . $model->duracion . '</span>
                    Lista de ' . $model->tipo->tipo_duracion . '
                </li>'
                .
                \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'summary' => '',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('_reducedView.php', ['model' => $model]);
                    },
                ])
                .
                '</ul>';

            $items[] = Utility::tabXOption($model->tipo->tipo_duracion, $str);
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

        <div class="row all-comments comentarios-order">

            <?php $form = ActiveForm::begin([
                'action' => Url::to([
                    'shows/view',
                    'id' => $model->id,
                ]),
                'method' => 'get',
            ]); ?>

            <div class="form-group col-md-4">
                <?=
                $form->field($searchModel, 'orderBy')
                    ->widget(Select2::class, [
                        'data' => $orderBy,
                        'options' => [
                            'placeholder' => 'Seleccione el tipo de ordenación...',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])
                    ->label(false);
                ?>
            </div>
            <div class="form-group col-md-3">
                <?=
                $form->field($searchModel, 'orderType')
                    ->widget(Select2::class, [
                        'data' => $orderType,
                        'options' => [
                            'placeholder' => 'Selecciona un tipo de show a buscar...',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
                    ->label(false);
                ?>
            </div>

            <div class="form-group col-md-3">
                <?= Html::submitButton('Ordenar', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>

        <div class="row all-comments">

            <?= Utility::formatComentarios($valoraciones, $this, $comentarioHijo) ?>

        </div>
    </div>
</div>
