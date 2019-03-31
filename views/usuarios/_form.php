<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\LoginForm */

use app\helpers\Utility;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Registrarse';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(Utility::togglePassword());
?>
<div class="panel-body panel-custom">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'nick', [
        'inputTemplate' => Utility::inputWithIcon('user'),
    ])->textInput([
        'autofocus' => true,
        'inputTemplate'
    ]) ?>
    <?= $form->field($model, 'email', [
        'inputTemplate' => Utility::inputWithIcon('envelope'),
    ])->textInput([
        'autofocus' => true,
        'inputTemplate'
    ]) ?>
    <?= $form->field($model, 'password', [
        'inputTemplate' => Utility::inputWithIcon('eye-close'),
    ])->passwordInput() ?>
    <?= $form->field($model, 'password_repeat', [
        'inputTemplate' => Utility::inputWithIcon('eye-close'),
    ])->passwordInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Registrarse', ['class' => 'btn btn-block btn-primary', 'name' => 'login-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>