<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\helpers\Utility;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

$css = <<<EOCSS
    .panel-custom {
        padding: 10px 30px;
    }
EOCSS;

$this->registerCss($css);
$this->registerJs(Utility::togglePassword());
?>
<div class="col-md-4 col-md-offset-4">
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
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>