<?php

use yii\helpers\Url;
use yii\helpers\Html;

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
        border: 2px solid gray;
        border-radius: 5px;
        height: 250px;
    }
    
    div.media-left.media-middle {
        object-fit: cover;
    }
EOCSS;

$this->registerCss($css);
?>

<div class="col-md-5">
    <?php
    if ($model->imagen_id !== null) {
        echo Html::img($model->imagen->link, ['alt' => 'Enlace roto', 'class' => 'img-thumbnail img-rounded']);
    }
    ?>
</div>

<div class="col-md-7">
    <h3 class="media-heading">
        <?=
        Html::a(Html::encode($model->titulo), ['shows/view', 'id' => $model->id]) .
        \kartik\rating\StarRating::widget([
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
    </h3>
    <?= Html::encode($model->sinopsis) ?>
</div>
