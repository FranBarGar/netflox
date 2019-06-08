<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Netflox';

?>
<div class="usuarios-create">
    <div class="col-md-12 col-xs-12">
        <div class="jumbotron">
            <h1>Bienvenido a Netflox</h1>
            <p class="lead">Autentifiquese o registrese para continuar.</p>
        </div>
    </div>
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-primary">
            <div class="panel-heading panel-heading-principal">
                <h3 class="panel-title">Registrarse</h3>
            </div>
            <div class="panel-body panel-custom">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
