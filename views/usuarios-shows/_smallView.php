<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosShows */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="usuarios-shows-view">

    <div class="col-xs-12 border-bottom-custom" style="padding-top: 5px; padding-bottom: 5px">
        <p>
            <?= Yii::t('app', $model->accion->accion, [
                'user' => Html::a($model->usuario->nick, ['usuarios/view', 'id' => Yii::$app->user->id]),
                'name' => Html::a($model->show->getFullTittle(), ['shows/view', 'id' => $model->show->id]),
                'date' => Yii::$app->formatter->asDate($model->created_at, 'long'),
            ]) ?>
        </p>
    </div>

</div>
