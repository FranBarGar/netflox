<?php

namespace app\controllers;

use Yii;
use app\models\Seguidores;
use app\models\SeguidoresSearch;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SeguidoresController implements the CRUD actions for Seguidores model.
 */
class SeguidoresController extends Controller
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
                        'actions' => ['view', 'index', 'create', 'get-seguidores', 'get-bloqueados', 'follow'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete', 'update',],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $relacion = Seguidores::findOne(Yii::$app->request->get('id'));
                            if ($relacion !== null) {
                                return $relacion->seguidor_id == Yii::$app->user->identity->id;
                            }
                            return false;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Seguidores models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SeguidoresSearch();
        $dataProvider = $searchModel->searchBlocked(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Seguidores model.
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
     * Finds the Seguidores model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Seguidores the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Seguidores::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Creates a new Seguidores model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Seguidores();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Seguidores model.
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
     * Deletes an existing Seguidores model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param $id
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
     * Lista de seguidores.
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetSeguidores()
    {
        $searchModel = new SeguidoresSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $search = Yii::$app->request->get('SeguidoresSearch');

        $seguidorId = isset($search['seguidor_id']) ? $search['seguidor_id'] : null;
//        $seguidoId = isset($search['seguido_id']) ? $search['seguido_id'] : null;

        if ($seguidorId != '') {
            $str = 'Siguiendo';
        } else {
            $str = 'Seguidores';
        }

        return $this->renderPartial('indexPartial.php', [
            'title' => $str,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lista de usuarios bloqueados.
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetBloqueados()
    {
        $searchModel = new SeguidoresSearch();
        $dataProvider = $searchModel->searchBlocked(Yii::$app->request->queryParams);

        return $this->renderPartial('indexPartial.php', [
            'title' => 'Usuarios bloqueados',
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Accion de seguir/dejar de seguir a un usuario
     *
     * @param $seguido_id
     *
     * @return string
     */
    public function actionFollow($seguido_id)
    {
        /** @var Seguidores $antiguo */
        $antiguo = Seguidores::find()
            ->andWhere([
                'seguido_id' => $seguido_id,
                'seguidor_id' => Yii::$app->user->id,
                'ended_at' => null,
            ])
            ->one();

        $opt = ['class' => 'btn-danger btn-success'];

        if ($antiguo != null) {
            $antiguo->ended_at = gmdate('Y-m-d H:i:s');
            $antiguo->save();
            $opt['tittle'] = 'Follow';
        } else {
            $model = new Seguidores();
            $model->seguidor_id = Yii::$app->user->id;
            $model->seguido_id = $seguido_id;
            $model->save();
            $opt['tittle'] = 'Unfollow';
        }

        return json_encode($opt);
    }
}
