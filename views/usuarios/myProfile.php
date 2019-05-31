<?php

use app\helpers\Utility;
use kartik\tabs\TabsX;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = $model->nick;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
\kartik\rating\StarRatingAsset::register($this);

$this->registerJs(Utility::AJAX_VOTAR);
$this->registerCss(Utility::CSS);
$valoracionesUrl = Url::to(['comentarios/get-valoraciones']);
$accionesUrl = Url::to(['usuarios-shows/get-acciones']);
$miId = $model->id;
$followingId = json_encode($followingId);
?>
<div class="usuarios-view">

    <div class="col-md-3">
        <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'class' => 'img-responsive img-circle', 'width' => '100%']) ?>

        <div class="row">
            <h2><?= Html::encode($model->nick) ?></h2>
            <div class="biografia-form">
                <?php $form = ActiveForm::begin(['action' => ['usuarios/update', 'id' => Yii::$app->user->id]]); ?>
                <?= $form->field($model, 'biografia')->textarea(['rows' => '9']) ?>
                <div class="form-group">
                    <?= Html::submitButton('Guardar', ['class' => 'btn btn-block btn-primary', 'name' => 'login-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-9">
<!--    MIS VALORACIONES    -->
        <?=
        Html::a('Mis valoraciones', $valoracionesUrl, [
            'class' => 'btn btn-primary col-md-3',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$valoracionesUrl',
                        data: {
                            'ids': $miId
                        },
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
        Html::a('Mis acciones', $accionesUrl, [
            'class' => 'btn btn-primary col-md-3',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$accionesUrl',
                        data: {
                            'ids': $miId
                        },
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

        <!--    VALORACIONES SEGUIDORES    -->
        <?=
        Html::a('Valoraciones de seguidores', 'comentarios/get-valoraciones', [
            'class' => 'btn btn-primary col-md-3',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$valoracionesUrl',
                        data: {
                            'ids': '$followingId'
                        },
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

        <!--    ACCIONES SEGUIDORES    -->
        <?=
        Html::a('Acciones de seguidores', 'comentarios/get-valoraciones', [
            'class' => 'btn btn-primary col-md-3',
            'onclick' => "
                    $.ajax({
                        type : 'GET',
                        url : '$accionesUrl',
                        data: {
                            'ids': '$followingId'
                        },
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
        <div id="content-container-custom"></div>
    </div>
</div>
