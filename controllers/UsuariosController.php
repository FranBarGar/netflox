<?php

namespace app\controllers;

use app\helpers\Utility;
use app\models\ComentariosSearch;
use app\models\Seguidores;
use app\models\SeguidoresSearch;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use app\models\UsuariosShows;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
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
                        'actions' => ['delete', 'index-admin'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->rol == 'admin';
                        }
                    ],
                    [
                        'actions' => ['update', 'view', 'view-blocked', 'index', 'activar', 'my-profile'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Usuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Usuarios models.
     * @return mixed
     */
    public function actionIndexAdmin()
    {
        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexAdmin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuarios model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if ($id == Yii::$app->user->id) {
            return $this->redirect(['usuarios/my-profile', 'id' => $id]);
        }

        $soyBloqueado = Seguidores::find()
                ->andWhere([
                    'seguido_id' => Yii::$app->user->id,
                    'seguidor_id' => $id,
                    'ended_at' => null,
                ])
                ->andWhere(['not', ['blocked_at' => null]])
                ->one() !== null;

        if ($soyBloqueado) {
            return $this->redirect(['usuarios/view-blocked', 'id' => $id]);
        }

        $seguidor = Seguidores::find()
                ->andWhere([
                    'seguido_id' => $id,
                    'seguidor_id' => Yii::$app->user->id,
                    'ended_at' => null
                ])
                ->one();

        if ($seguidor !== null) {
            $esSeguidor = $seguidor->blocked_at === null;
            $esBloqueado = $seguidor->blocked_at !== null;
        } else {
            $esSeguidor = $esBloqueado = false;
        }

        return $this->render('view', [
            'esSeguidor' => $esSeguidor,
            'esBloqueado' => $esBloqueado,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single Usuarios model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewBlocked($id)
    {
        if ($id == Yii::$app->user->id) {
            return $this->redirect(['usuarios/my-profile', 'id' => $id]);
        }

        $soyBloqueado = Seguidores::find()
                ->andWhere([
                    'seguido_id' => Yii::$app->user->id,
                    'seguidor_id' => $id,
                    'ended_at' => null,
                ])
                ->andWhere(['not', ['blocked_at' => null]])
                ->one() !== null;

        if (!$soyBloqueado) {
            return $this->redirect(['usuarios/view', 'id' => $id]);
        }

        $seguidor = Seguidores::find()
                ->andWhere([
                    'seguido_id' => $id,
                    'seguidor_id' => Yii::$app->user->id,
                    'ended_at' => null
                ])
                ->one();

        if ($seguidor !== null) {
            $esBloqueado = $seguidor->blocked_at !== null;
        } else {
            $esBloqueado = false;
        }

        return $this->render('viewBlocked', [
            'esBloqueado' => $esBloqueado,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Usuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Usuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Displays a single Usuarios model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionMyProfile($id)
    {
        $searchModel = new ComentariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($id != Yii::$app->user->id) {
            return $this->redirect(['usuarios/view', 'id' => $id]);
        }

        return $this->render('myProfile', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Usuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Usuarios(['scenario' => Usuarios::SCENARIO_CREATE]);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $url = Url::to([
                'usuarios/activar',
                'id' => $model->id,
                'token' => $model->token,
            ], true);

            $cuerpo = "<h3>Pulsa el siguiente enlace para activar al usuario:</h3>
            <a href=\"$url\">Validar usuario</a>";

            if (Utility::enviarMail($cuerpo, $model->email, 'Activar usuario')) {
                Yii::$app->session->setFlash('success', 'Se ha enviado un correo a su cuenta de email, por favor verifique su cuenta.');
            } else {
                Yii::$app->session->setFlash('error', 'Ha habido un error al mandar el correo.');
            }

            return $this->redirect('site/index');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Usuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
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
     * Deletes an existing Usuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Activa una cuenta que se aun no ha sido verificada.
     * @param  int $id ID de la cuenta a verificar.
     * @param  string $token Token asociado a la cuenta aun no verificada
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionActivar($id, $token)
    {
        $usuario = $this->findModel($id);
        if ($usuario->token === $token) {
            $usuario->token = null;
            $usuario->save();
            Yii::$app->session->setFlash('success', 'Usuario validado. Inicie sesión.');
            return $this->redirect(['site/login']);
        }
        Yii::$app->session->setFlash('error', 'La validación no es correcta.');
        return $this->redirect(['site/index']);
    }
}
