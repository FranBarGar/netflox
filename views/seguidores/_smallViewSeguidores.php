<?php

use app\models\Seguidores;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Seguidores */

\yii\web\YiiAsset::register($this);
$seguido = $model->seguido;
$seguidor = $model->seguidor;

$follow = Url::to(['seguidores/follow', 'seguido_id' => $seguidor->id]);
$block = Url::to(['seguidores/block', 'seguido_id' => $seguidor->id]);

$soySeguidor = Seguidores::soySeguidor($seguidor->id);
?>
<div class="usuarios-shows-view">

    <div class="col-md-12 col-xs-12 border-bottom-custom" style="padding-top: 5px; padding-bottom: 5px;">
        <div class="col-xs-3 text-center">
            <?= Html::img($seguidor->getImagenLink(), ['alt' => 'Enlace roto', 'class' => 'img-responsive img-circle', 'width' => '100%']) ?>
            <?=
            Html::a(($soySeguidor ? 'Unfollow' : 'Follow'), $follow, [
                'class' => 'btn col-md-6 col-xs-12 ' . ($soySeguidor ? 'btn-danger' : 'btn-success'),
                'onclick' => "
                    event.preventDefault();
                    btn = this;
                    $.ajax({
                        type : 'GET',
                        url : '$follow',
                        success: function(data) {
                            data = JSON.parse(data);
                            if (data == '') {
                                $.notify({
                                    title: 'Error:',
                                    message: 'El usuario te ha bloqueado.'
                                }, {
                                    type: 'danger'
                                });
                            } else {
                                $(btn).html(data.tittle).toggleClass(data.class);
                            }
                        }
                    });
                    return false;
                ",
            ]);
            ?>
            <?=
            Html::a('Bloquear', $block, [
                'class' => 'btn col-md-6 col-xs-12 btn-danger',
                'onclick' => "
                    event.preventDefault();
                    btn = this;
                    $.ajax({
                        type : 'GET',
                        url : '$block',
                        success: function(data) {
                            location.reload();
                        }
                    });
                    return false;
                ",
            ]);
            ?>
        </div>
        <div class="col-xs-9">
            <h3 style="margin: 0"><?= Html::a($seguidor->nick, ['usuarios/view', 'id' => $seguidor->id]) ?></h3>
            <small>Seguidor de <?= Html::a($seguido->nick, ['usuarios/view', 'id' => $seguido->id]) ?> desde el <?= Yii::$app->formatter->asDate($model->created_at, 'long') ?></small>
            <p>
                <?= Html::encode($seguidor->biografia) ?>
            </p>
        </div>
    </div>

</div>
