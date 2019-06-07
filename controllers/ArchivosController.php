<?php

namespace app\controllers;

use app\helpers\Utility;
use Throwable;
use Yii;
use app\models\Archivos;
use app\models\ArchivosSearch;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\grid\GridView;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArchivosController implements the CRUD actions for Archivos model.
 */
class ArchivosController extends Controller
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
     * Descarga un archivo de la maquina.
     *
     * @param $id
     *
     * @return \yii\console\Response|\yii\web\Response
     *
     * @throws NotFoundHttpException
     */
    public function actionFile($id)
    {
        $archivo = Archivos::findOne($id);

        if ($archivo !== null) {
            $archivo->num_descargas += 1;
            $archivo->save();

            try {
                $file = Utility::s3Download($archivo->link, 'netflox-shows-content');
                $path = Yii::getAlias('@content/' . $archivo->link);
                file_put_contents($path, $file['Body']);
                return Yii::$app->response->sendFile($path);
            } catch (\Exception $exception) {
                throw new NotFoundHttpException('El fichero no existe.');
            }
        }

        throw new NotFoundHttpException('El fichero no existe.');
    }

    /**
     * Lists all Archivos models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArchivosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Archivos model.
     *
     * @param integer $id
     *
     * @return mixed
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Archivos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Archivos the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Archivos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Creates a new Archivos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Archivos();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Participantes model.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function actionAjaxCreate()
    {
        $model = new Archivos();
        $model->show_id = Yii::$app->request->post('show_id');
        $model->link = Yii::$app->request->post('link');
        $model->descripcion = Yii::$app->request->post('descripcion');

        if ($model->save()) {
            $archivosProvider = (new ArchivosSearch())->search(Yii::$app->request->queryParams, $model->show_id);

            return json_encode(GridView::widget([
                'summary' => '',
                'dataProvider' => $archivosProvider,
                'columns' => [
                    'descripcion',
                    'link',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                return '<span id="' . $model->id . '" class="glyphicon glyphicon-trash archivos-delete"></span>';
                            }

                        ],
                    ],
                ],
            ]));
        }

        return json_encode('');
    }

    /**
     * Updates an existing Archivos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Archivos model.
     *
     * @return false|string
     *
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete()
    {
        $model = $this->findModel(Yii::$app->request->post('id'));

        $archivosProvider = (new ArchivosSearch())->search(Yii::$app->request->queryParams, $model->show_id);

        $model->delete();

        return json_encode(GridView::widget([
            'summary' => '',
            'dataProvider' => $archivosProvider,
            'columns' => [
                'descripcion',
                'link',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            return '<span id="' . $model->id . '" class="glyphicon glyphicon-trash archivos-delete"></span>';
                        }

                    ],
                ],
            ],
        ]));
    }
}
