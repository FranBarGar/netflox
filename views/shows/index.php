<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ShowsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shows';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_search', [
        'model' => $searchModel,
        'listaTipos' => $listaTipos,
        'listaGeneros' => $listaGeneros,
        'orderBy' => $orderBy,
        'orderType' => $orderType,
    ]);
    ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'itemOptions' => ['class' => 'shows-smallView col-md-12 media'],
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_smallView.php', ['model' => $model]);
        },
    ]) ?>


</div>
