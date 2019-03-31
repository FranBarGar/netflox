<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>

<div class="shows-reducedView-<?= $model->id ?>">

    <li class="list-group-item">
        <span class="badge">No se que poner aqui</span>
        <?= Html::a(Html::encode($model->titulo), ['shows/view', 'id' => $model->id]) ?>
    </li>

</div>
