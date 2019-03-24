<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShowsDescargasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shows Descargas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-descargas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Shows Descargas', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'num_descargas',
            'archivo_id',
            'show_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
