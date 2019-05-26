<?php

namespace app\controllers;

use app\helpers\Utility;
use app\models\Comentarios;
use app\models\ComentariosSearch;
use app\models\Participantes;
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

        return $this->render('index', [
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
            ->andFilterWhere(['show_id' => $id])
            ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => Shows::findChildrens($id),
        ]);

        return $this->render('view', [
            'model' => $this->advancedFindModel($id),
            'dataProvider' => $dataProvider,
            'comentarioHijo' => Comentarios::getEmpty($id),
            'searchModel' => $searchModel,
            'valoraciones' => $valoracionesProvider,
            'valoracion' => Comentarios::findOrEmpty($id),
            'accion' => UsuariosShows::findOrEmpty($id),
            'listaAcciones' => Utility::listaAcciones(),
            'orderBy' => Comentarios::ORDER_BY,
            'orderType' => Utility::ORDER_TYPE,
        ]);
    }

    /**
     * Finds the Shows model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shows the loaded model
     * @throws NotFoundHttpException if the model cannot be found
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
        $model = new Shows();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /**
             * Subimos la imagen y se la añadimos a el modelo.
             */
            $model->imgUpload = UploadedFile::getInstance($model, 'imgUpload');
            if ($model->imgUpload !== null) {
                $model->uploadImg();
                $model->imgUpload = null;
            }

            /**
             * Guardamos el modelo tras añadirle todos los campos necesarios para obtener el ID.
             */
            $model->save();

            /**
             * Añadimos generos al show actual.
             */
            if (!empty($model->listaGeneros)) {
                foreach ($model->listaGeneros as $genero_id) {
                    $show_generos = new ShowsGeneros();
                    $show_generos->show_id = $model->id;
                    $show_generos->genero_id = $genero_id;
                    $show_generos->save();
                }
            }

            /**
             * Añadimos los links de descarga al show.
             */
            $model->showUpload = UploadedFile::getInstance($model, 'showUpload');
            if ($model->showUpload !== null) {
                $model->uploadShow();
                $model->showUpload = null;
            }

            /**
             * Añadimos los participantes
             */
            $model->listaParticipantes = json_decode($model->listaParticipantes);
            if (!empty($model->listaParticipantes)) {
                foreach ($model->listaParticipantes as $rolId => $personas) {
                    if (!empty($personas)) {
                        foreach ($personas as $personaId) {
                            $participantes = new Participantes();
                            $participantes->show_id = $model->id;
                            $participantes->persona_id = $personaId;
                            $participantes->rol_id = $rolId;
                            $participantes->save();
                        }
                    }
                }
            }

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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $listaPadres['listaPadres'] =
            $model->tipo->padre_id !== null
                ? Utility::listaPadres($model->tipo->padre_id)
                : [];

        return $this->render('update', [
            'model' => $model,
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
     * @param $id
     * @return array|bool
     */
    public function actionAjaxCreateInfo($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $tipo = Tipos::findOne($id);
        $info = [$tipo->tipo_duracion];

        if (($padre_id = $tipo->padre_id) !== null) {
            $info[] = Shows::find()
                ->select('titulo')
                ->where(['tipo_id' => $padre_id])
                ->indexBy('id')
                ->column();
        } else {
            $info[] = false;
        }

        return json_encode($info);
    }
}
