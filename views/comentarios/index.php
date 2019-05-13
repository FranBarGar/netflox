<?php

use app\helpers\Utility;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ComentariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Valoraciones';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(Utility::AJAX_VOTAR);
$this->registerCss(Utility::CSS);
?>
<div class="row comentarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row all-comments">
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('valoracionView.php', ['model' => $model]);
            },
        ]) ?>
    </div>

</div>
