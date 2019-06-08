<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\helpers\Utility;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$css = <<<EOCSS
    .panel-custom {
        padding: 10px 30px;
    }
EOCSS;

$this->registerCss($css);
$this->registerJs(Utility::togglePassword());
?>
<div class="col-md-12 col-xs-12">
    <div class="jumbotron">
        <h1>Bienvenido a Netflox</h1>
        <p class="lead">Autentifiquese o registrese para continuar.</p>
    </div>
</div>
<div class="col-md-4 col-md-offset-4 col-xs-12">
    <div class="panel panel-primary">
        <div class="panel-heading panel-heading-principal">
            <h3 class="panel-title">Iniciar sesión</h3>
        </div>
        <div class="panel-body panel-custom">
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'username', [
                    'inputTemplate' => Utility::inputWithIcon('user'),
                    ]) ?>
                <?= $form->field($model, 'password', [
                    'inputTemplate' => Utility::inputWithIcon('eye-close'),
                    ])->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox([
                    'template' => "{input} {label}",
                ]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Iniciar sesión', ['class' => 'btn btn-block btn-primary', 'name' => 'login-button']) ?>
                    <?= Html::a('Resgistrarse', ['usuarios/create'], [
                            'class' => 'btn btn-block btn-primary'
                    ]) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>