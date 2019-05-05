<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosShowsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios Shows';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-shows-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Usuarios Shows', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'usuario_id',
            'show_id',
            'plan_to_watch',
            'droppped',
            //'watched',
            //'watching',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
