<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShowsGenerosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shows Generos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-generos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Shows Generos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'show_id',
            'genero_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
