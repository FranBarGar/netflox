<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

\yii\web\YiiAsset::register($this);

$seguido = $model->seguido;
$seguidor = $model->seguidor;

$block = Url::to(['seguidores/block', 'seguido_id' => $seguido->id]);
?>
<div class="usuarios-shows-view">

    <div class="col-xs-12 col-md-12 border-bottom-custom" style="padding-top: 5px; padding-bottom: 5px">
        <div class="col-xs-3 text-center">
            <?= Html::img($seguido->getImagenLink(), ['alt' => 'Enlace roto', 'class' => 'img-responsive img-circle', 'width' => '100%']) ?>
            <?=
            Html::a('Desbloquear', $block, [
                'class' => 'btn col-md-12 col-xs-12 btn-success',
                'onclick' => "
                    event.preventDefault();
                    btn = this;
                    $.ajax({
                        type : 'GET',
                        url : '$block',
                        success: function(data) {
                            $(btn).parent().parent().remove();
                            sessionStorage.setItem('blockData', data);
                        }
                    });
                    return false;
                ",
            ]);
            ?>
        </div>
        <div class="col-xs-9">
            <h3 style="margin: 0"><?= Html::a($seguido->nick, ['usuarios/view', 'id' => $seguido->id]) ?></h3>
            <small>Bloqueado por <?= Html::a($seguidor->nick, ['usuarios/view', 'id' => $seguidor->id]) ?> desde el <?= Yii::$app->formatter->asDate($model->created_at, 'long') ?></small>
            <p>
                <?= Html::encode($seguido->biografia) ?>
            </p>
        </div>
    </div>

</div>
