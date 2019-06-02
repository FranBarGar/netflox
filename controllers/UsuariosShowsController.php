<?php

namespace app\controllers;

use Yii;
use app\models\UsuariosShows;
use app\models\UsuariosShowsSearch;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsuariosShowsController implements the CRUD actions for UsuariosShows model.
 */
class UsuariosShowsController extends Controller
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
                        'actions' => ['create', 'get-acciones', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete', 'index', 'view'],
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
     * Lists all UsuariosShows models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuariosShowsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UsuariosShows model.
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
     * Finds the UsuariosShows model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UsuariosShows the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UsuariosShows::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Creates a new UsuariosShows model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        /** @var UsuariosShows $antiguo */
        $antiguo = UsuariosShows::find()
            ->andWhere([
                'usuario_id' => Yii::$app->user->id,
                'show_id' => $id,
                'ended_at' => null,
            ])
            ->one();

        $model = new UsuariosShows();

        if ($model->load(Yii::$app->request->post())) {
            if ($antiguo != null) {
                if ($model->accion_id == '') {
                    $antiguo->ended_at = gmdate('Y-m-d H:i:s');
                    $antiguo->save();
                } elseif ($model->accion_id != $antiguo->accion_id) {
                    $antiguo->ended_at = gmdate('Y-m-d H:i:s');
                    $antiguo->save();
                    $model->save();
                }
            } elseif ($model->accion_id != '') {
                $model->save();
            }
        }

        return $this->redirect(['shows/view', 'id' => $id]);
    }

    /**
     * Lists all UsuariosShows models.
     *
     * @param $ids
     *
     * @return string
     *
     * @throws \Exception
     */
    public function actionGetAcciones()
    {
        $searchModel = new UsuariosShowsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $ids = Yii::$app->request->get('UsuariosShowsSearch')['usuario_id'];

        if (is_array($ids)) {
            $str = 'Acciones de seguidos';
        } elseif (Yii::$app->user->id == $ids) {
            $str = 'Mis acciones';
        } else {
            $str = 'Acciones';
        }

        return $this->renderPartial('index.php', [
            'title' => $str,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing UsuariosShows model.
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

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UsuariosShows model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
