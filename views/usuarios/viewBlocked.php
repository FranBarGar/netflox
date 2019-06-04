<?php

use app\helpers\Utility;
use kartik\tabs\TabsX;
use kartik\widgets\FileInput;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = $model->nick;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
\app\assets\AlertAsset::register($this);

$block = Url::to(['seguidores/block', 'seguido_id' => $model->id]);

?>
<div class="usuarios-view">

    <div class="col-md-3 col-xs-12">
        <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'class' => 'img-responsive img-circle', 'width' => '100%']) ?>

        <div class="row">
            <?=
            Html::a(($esBloqueado ? 'Desbloquear' : 'Bloquear'), $block, [
                'class' => 'btn col-md-12 col-xs-12' . ($esBloqueado ? ' btn-success' : ' btn-danger'),
                'onclick' => "
                    event.preventDefault();
                    if(
                    $(this).html() == 'Desbloquear' ||
                    confirm('¿Estas seguro de bloquear a este usuario? Esto denegara el acceso a esta persona a tu perfil y no podras seguirlo.')
                    ) {
                        btn = this;
                        $.ajax({
                            type : 'GET',
                            url : '$block',
                            success: function(data) {
                                location.reload();
                            }
                        });
                    }
                    return false;
                ",
            ]);
            ?>
        </div>

        <div class="row">
            <div class="col-xs-12 text-center" style="margin-top: 5px">
                <p>Usuario desde el <?= Yii::$app->formatter->asDate($model->created_at, 'long') ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-9 col-xs-12 jumbotron">
        <h1 style="color: indianred"><strong>Bloqueado</strong></h1>
        <p>Este usuario te tiene bloqueado.</p>
        <p>No podras acceder a su informacion ni seguir a este usuario.</p>
    </div>
</div>
