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
\kartik\rating\StarRatingAsset::register($this);

$ajax = <<<EOJS
    $('div#content-container-custom ').on('click', '.pagination li a', (e) => {
        $.ajax({
            type : 'GET',
            url : e.target.href,
            success: function(response) {
                $('#content-container-custom').html(response);
                $('.voto').on('click', votar);
                $('input.rating-loading').rating('create', {
                        'size': 'sm',
                        'readonly': true,
                        'showClear': false,
                        'showCaption': false,
                    });
            }
        });
        return false;
    });
EOJS;


$this->registerJs(Utility::AJAX_VOTAR . $ajax);
$this->registerCss(Utility::CSS);

$follow = Url::to(['seguidores/follow', 'seguido_id' => $model->id]);
$block = Url::to(['seguidores/block', 'seguido_id' => $model->id]);

/**
 * Url de valoraciones
 */
$misValoracionesUrl = Url::to(['comentarios/get-valoraciones', 'ComentariosSearch[usuario_id]' => $model->id]);
$valoracionesUrl = Url::to(['comentarios/get-valoraciones', 'ComentariosSearch[usuario_id]' => $followingId]);

/**
 * Url de seguidores
 */
$seguidoresUrl = Url::to(['seguidores/get-seguidores', 'SeguidoresSearch[seguido_id]' => $model->id]);
$seguidosUrl = Url::to(['seguidores/get-seguidores', 'SeguidoresSearch[seguidor_id]' => $model->id]);

/**
 * Url de acciones
 */
$misAccionesUrl = Url::to(['usuarios-shows/get-acciones', 'UsuariosShowsSearch[usuario_id]' => $model->id]);
$accionesUrl = Url::to(['usuarios-shows/get-acciones', 'UsuariosShowsSearch[usuario_id]' => $followingId]);
?>
<div class="usuarios-view">

    <div class="col-md-3 col-xs-12">
        <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'class' => 'img-responsive img-circle', 'width' => '100%']) ?>

        <div class="row">
            <?=
            Html::a(($esSeguidor ? 'Unfollow' : 'Follow'), $follow, [
                'class' => 'btn col-md-6 col-xs-6 ' . ($esSeguidor ? 'btn-danger' : 'btn-success'),
                'style' => ($esBloqueado ? 'display: none' : 'display: block'),
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
            Html::a(($esBloqueado ? 'Desbloquear' : 'Bloquear'), $block, [
                'class' => 'btn ' . ($esBloqueado ? 'col-md-12 col-xs-12 btn-success' : 'col-md-6 col-xs-6 btn-danger'),
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
            <h2><?= Html::encode($model->nick) ?></h2>
            <label for="biografia">Biografía:</label>
            <p><?= Html::encode($model->biografia) ?></p>
        </div>
    </div>

    <div class="col-md-9 col-xs-12">
        <!-- SEGUIDORES -->
        <?=
        Html::a('Seguidores', $seguidoresUrl, [
            'class' => 'btn btn-primary col-md-6 col-xs-6',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$seguidoresUrl',
                        success: function(response) {
                            $('#content-container-custom').html(response);
                        }
                    });
                    return false;
                ",
        ]);
        ?>

        <!-- SEGUIDOS -->
        <?=
        Html::a('Seguidos', $seguidosUrl, [
            'class' => 'btn btn-primary col-md-6 col-xs-6',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$seguidosUrl',
                        success: function(response) {
                            $('#content-container-custom').html(response);
                        }
                    });
                    return false;
                ",
        ]);
        ?>

        <!--    MIS VALORACIONES    -->
        <?=
        Html::a('Valoraciones', $misValoracionesUrl, [
            'class' => 'btn btn-primary col-md-6 col-xs-6',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$misValoracionesUrl',
                        success: function(response) {
                            $('#content-container-custom').html(response);
                            $('.voto').on('click', votar);
                            $('input.rating-loading').rating('create', {
                                    'size': 'sm',
                                    'readonly': true,
                                    'showClear': false,
                                    'showCaption': false,
                                });
                        }
                    });
                    return false;
                ",
        ]);
        ?>

        <!--    MIS ACCIONES    -->
        <?=
        Html::a('Acciones', $misAccionesUrl, [
            'class' => 'btn btn-primary col-md-6 col-xs-6',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$misAccionesUrl',
                        success: function(response) {
                            $('#content-container-custom').html(response);
                        }
                    });
                    return false;
                ",
        ]);
        ?>
        <div id="content-container-custom"></div>
    </div>
</div>
