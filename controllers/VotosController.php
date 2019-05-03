<?php

namespace app\controllers;

use app\models\Comentarios;
use Yii;
use app\models\Votos;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * VotosController implements the CRUD actions for Votos model.
 */
class VotosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['votar',],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new Votos model or Updates an existing Votos model.
     * @return array
     */
    public function actionVotar()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        /** @var Votos $model */
        $model = Votos::find()
            ->andFilterWhere([
                'usuario_id' => Yii::$app->user->id,
                'comentario_id' => Yii::$app->request->post('comentario_id')
            ])
            ->one();

        if ($model !== null) {
            if (($votacion = Yii::$app->request->post('votacion')) == $model->votacion) {
                $model->votacion = 0;
            } else {
                $model->votacion = $votacion;
            }
        } else {
            $model = new Votos();

            $model->comentario_id = Yii::$app->request->post('comentario_id');
            $model->usuario_id = Yii::$app->user->id;
            $model->votacion = Yii::$app->request->post('votacion');
        }

        if ($model->save()) {
            $comentario = Comentarios::findOne($model->comentario_id);

            if ($model->votacion == 1) {
                $comentario->likes = '<b>' . $comentario->likes . '</b>';
            } elseif ($model->votacion == -1) {
                $comentario->dislikes = '<b>' . $comentario->dislikes . '</b>';
            }

            return json_encode([
                'opc' => $model->votacion,
                'likes' => $comentario->likes,
                'dislikes' => $comentario->dislikes,
            ]);
        }

        return json_encode([]);
    }
}
