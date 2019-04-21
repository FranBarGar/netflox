<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GestoresArchivosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gestores de archivos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gestores-archivos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'nombre',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
