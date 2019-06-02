<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosShowsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="comentarios-index">

    <div class="col-xs-12 border-bottom-custom" style="margin-top: 5px">
        <h1> <?= $title ?> </h1>
    </div>

    <?=
    \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_valoracionView.php', ['model' => $model]);
        },
    ])
    ?>

</div>
