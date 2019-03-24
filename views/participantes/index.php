<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ParticipantesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Participantes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="participantes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Participantes', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'show_id',
            'persona_id',
            'rol_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
