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
?>

<div class="shows-smallView media">

    <div class="media-left media-middle text-center">
        <?php
        if ($model->imagen_id!==null) {
            echo Html::img($model->imagen->link, ['alt' => 'Enlace roto', 'width' => '200px', 'class' => 'media-object']);
        }
        ?>
    </div>

    <div class="media-body">
        <h1 class="media-heading">
            <?= Html::a(Html::encode($model->titulo), ['shows/view', 'id' => $model->id]) ?>
        </h1>
        <?= Html::encode($model->sinopsis) ?>
    </div>

</div>
