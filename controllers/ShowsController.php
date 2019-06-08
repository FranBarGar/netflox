<?php

namespace app\controllers;

use app\helpers\Utility;
use app\models\ArchivosSearch;
use app\models\Comentarios;
use app\models\ComentariosSearch;
use app\models\Participantes;
use app\models\ParticipantesSearch;
use app\models\ShowsGeneros;
use app\models\Tipos;
use app\models\UsuariosShows;
use Yii;
use app\models\Shows;
use app\models\ShowsSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * ShowsController implements the CRUD actions for Shows model.
 */
class ShowsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'ajax-create-info'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete', 'update', 'create',],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->rol == 'admin';
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Shows models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ShowsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (isset(Yii::$app->request->get('ShowsSearch')['tipo_id'])) {
            $tipo_id = Yii::$app->request->get('ShowsSearch')['tipo_id'];
        } else {
            $tipo_id = null;
        }

        $tipo = Tipos::findOne($tipo_id);
        if ($tipo !== null) {
            $title = $tipo->tipo;
        }

        return $this->render('index', [
            'title' => (isset($title) ? $title : 'Show') . 's',
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'listaTipos' => Utility::listaTiposSearch(),
            'listaGeneros' => Utility::listaGeneros(),
            'listaAcciones' => Utility::listaAcciones(),
            'orderBy' => Shows::ORDER_BY,
            'orderType' => Utility::ORDER_TYPE,
        ]);
    }

    /**
     * Displays a single Shows model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new ComentariosSearch();
        $valoracionesProvider = $searchModel
            ->search(Yii::$app->request->queryParams)
            ->query
            ->andFilterWhere(['comentarios.show_id' => $id])
            ->all();

        $model = $this->advancedFindModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->findChildrens(),
        ]);

        ($valoracion = Comentarios::findOrEmpty($id))->setScenario(Comentarios::SCENARIO_VALORAR);
        ($comentario = Comentarios::getEmpty($id))->setScenario(Comentarios::SCENARIO_COMENTAR);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'comentarioHijo' => $comentario,
            'searchModel' => $searchModel,
            'valoraciones' => $valoracionesProvider,
            'valoracion' => $valoracion,
            'accion' => UsuariosShows::findOrEmpty($id),
            'listaAcciones' => Utility::listaAcciones(),
            'orderBy' => Comentarios::ORDER_BY,
            'orderType' => Utility::ORDER_TYPE,
        ]);
    }

    /**
     * Finds the Shows model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    protected function advancedFindModel($id)
    {
        $model = Shows::find()
            ->select('
            shows.*, 
            SUM(COALESCE(valoracion, 0))/GREATEST(COUNT(valoracion), 1)::float AS "valoracionMedia"')
            ->joinWith('comentarios')
            ->joinWith('generos')
            ->with('tipo')
            ->with('archivos')
            ->where(['shows.id' => $id])
            ->groupBy('shows.id')
            ->one();


        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Creates a new Shows model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Shows(['scenario' => Shows::SCENARIO_CREATE]);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'listaTipos' => Utility::listaTipos(),
            'listaGeneros' => Utility::listaGeneros(),
            'listaPersonas' => Utility::listaPersonas(),
            'listaRoles' => Utility::listaRoles(),
        ]);
    }

    /**
     * Updates an existing Shows model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $participantesProvider = (new ParticipantesSearch())->search(Yii::$app->request->queryParams, $id);
        $archivosProvider = (new ArchivosSearch())->search(Yii::$app->request->queryParams, $id);

        $listaPadres['listaPadres'] =
            $model->tipo->padre_id !== null
                ? Utility::listaPadres($model->tipo->padre_id)
                : [];

        $model->listaGeneros = Utility::listaGenerosId($id);

        return $this->render('update', [
            'model' => $model,
            'participantesProvider' => $participantesProvider,
            'archivosProvider' => $archivosProvider,
            'listaTipos' => Utility::listaTipos(),
            'listaGeneros' => Utility::listaGeneros(),
            'listaPersonas' => Utility::listaPersonas(),
            'listaRoles' => Utility::listaRoles(),
        ]);
    }

    /**
     * Finds the Shows model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shows the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shows::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Deletes an existing Shows model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Devuelve la informacion necesaria para la creacion o actualizacion del tipo de un show.
     * @param $id
     * @return array|bool
     */
    public function actionAjaxCreateInfo($id, $show_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $tipo = Tipos::findOne($id);
        $info = [$tipo->tipo_duracion];

        if (($padre_id = $tipo->padre_id) !== null) {
            $info[] = Shows::find()
                ->select('titulo')
                ->where(['tipo_id' => $padre_id])
                ->andWhere(['not', ['id' => $show_id]])
                ->indexBy('id')
                ->column();
        } else {
            $info[] = false;
        }

        return json_encode($info);
    }
}
