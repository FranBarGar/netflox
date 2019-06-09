<?php

use app\helpers\Utility;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
\app\assets\AlertAsset::register($this);

$this->registerJs(Utility::JS_BLOCK);
$this->registerCss(Utility::CSS);
?>
<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_search', [
        'model' => $searchModel,
    ]);
    ?>

    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_smallView.php', ['model' => $model]);
        },
    ]); ?>


</div>
