<?php

use app\helpers\Utility;
use kartik\tabs\TabsX;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = $model->nick;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="usuarios-view">

    <div class="col-md-3">
        <?= Html::img($model->getImagenLink(), ['alt' => 'Enlace roto', 'class' => 'img-responsive img-circle', 'width' => '100%']) ?>

        <?php
            if (Yii::$app->user->id != $model->id):

        ?>

        <?php endif; ?>

        <div class="row">
            <h2><?= Html::encode($model->nick) ?></h2>

            <?php if (Yii::$app->user->id == $model->id): ?>
                <div class="biografia-form">
                    <?php $form = ActiveForm::begin(['action' => ['usuarios/update', 'id' => Yii::$app->user->id]]); ?>
                    <?= $form->field($model, 'biografia')->textarea(['rows' => '9']) ?>
                    <div class="form-group">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-block btn-primary', 'name' => 'login-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php else: ?>
                <label for="biografia">Biografia</label>
                <p id="biografia"><?= Html::encode($model->biografia ?: 'Sin biografia aun.') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-9">
        <?php
        $items = [];
        $items[] = Utility::tabXOption('Sinopsis', '<p>' . Html::encode($model->biografia) . '</p>');
        ?>

        <?=
        TabsX::widget([
            'items' => $items,
            'position' => TabsX::POS_ABOVE,
            'bordered' => true,
            'encodeLabels' => false
        ]);
        ?>
    </div>
</div>
