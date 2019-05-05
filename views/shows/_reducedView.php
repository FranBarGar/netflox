<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>

<div class="shows-reducedView-<?= $model->id ?>">

    <li class="list-group-item">
<!--        <span class="badge"> --><?php //// echo $model->findChildrens($model->id)->count() . '/' . $model->duracion ?><!--</span>-->
        <?= Html::a(Html::encode($model->titulo), ['shows/view', 'id' => $model->id]) ?>
    </li>

</div>
