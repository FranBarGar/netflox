<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SeguidoresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Seguidores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seguidores-index">

    <div class="col-xs-12 border-bottom-custom" style="margin-top: 5px">
        <h1> <?= $title ?> </h1>
    </div>
    <hr>
    <?=
    strpos($title, 'Seguidores') !== false ?
        \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_smallViewSeguidores.php', ['model' => $model]);
            },
        ])
        :
        \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('_smallViewSeguidos.php', ['model' => $model]);
            },
        ])
    ?>


</div>
