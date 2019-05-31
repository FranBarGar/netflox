<?php

namespace app\controllers;

use app\helpers\Utility;
use Yii;
use app\models\Comentarios;
use app\models\ComentariosSearch;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ComentariosController implements the CRUD actions for Comentarios model.
 */
class ComentariosController extends Controller
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
                        'actions' => ['view', 'index', 'create', 'valorar', 'get-valoraciones'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update', 'valorar-update'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $comentario = Comentarios::findOne(Yii::$app->request->get('id'));
                            if ($comentario !== null) {
                                return $comentario->usuario_id == Yii::$app->user->id;
                            }
                            return false;
                        }
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->identity->rol == 'admin') {
                                return true;
                            }

                            $comentario = Comentarios::findOne(Yii::$app->request->get('id'));
                            if ($comentario !== null) {
                                return $comentario->usuario_id == Yii::$app->user->id;
                            }
                            return false;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Comentarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ComentariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comentarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Comentarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comentarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comentarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Creates a new Comentarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Comentarios(['scenario' => Comentarios::SCENARIO_COMENTAR]);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
        }

        return $this->redirect(['shows/view', 'id' => $model->show_id]);
    }

    /**
     * Creates a new Comentarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionValorar()
    {
        $model = new Comentarios(['scenario' => Comentarios::SCENARIO_VALORAR]);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
        }

        return $this->redirect(['shows/view', 'id' => $model->show_id]);
    }

    /**
     * Updates an existing Comentarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->scenario = Comentarios::SCENARIO_COMENTAR;

        if ($model->load(Yii::$app->request->post())) {
            $model->edited_at = gmdate('Y-m-d H:i:s');
            $model->save();
        }

        return $this->redirect(['shows/view', 'id' => $model->show_id]);
    }

    /**
     * Updates an existing Comentarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionValorarUpdate($id)
    {
        $model = $this->findModel($id);

        $model->scenario = Comentarios::SCENARIO_VALORAR;

        if ($model->load(Yii::$app->request->post())) {
            $model->edited_at = gmdate('Y-m-d H:i:s');
            $model->save();
        }

        return $this->redirect(['shows/view', 'id' => $model->show_id]);
    }

    /**
     * @param $ids
     * @return string
     * @throws \Exception
     */
    public function actionGetValoraciones($ids)
    {
        $ids = json_decode($ids);
        $searchModel = new ComentariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $ids);

        if (is_array($ids)) {
            $str = 'Valoraciones de seguidores';
        } elseif (Yii::$app->user->id == $ids) {
            $str = 'Mis valoraciones';
        } else {
            $str = 'Valoraciones de ' .
                Html::a(Yii::$app->user->identity->nick, [
                    'usuarios/view',
                    'id' => $ids
                ]);
        }

        return '<div class="col-xs-12"><h1>' . $str . '</h1></div><hr>' .
            \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'summary' => '',
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->renderPartial('_valoracionViewWithShowName.php', ['model' => $model]);
                },
            ]);
    }

    /**
     * Deletes an existing Comentarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $comentario = $this->findModel($id);
        $show = $comentario->show_id;
        $comentario->delete();

        return $this->redirect(['shows/view', 'id' => $show]);
    }
}
