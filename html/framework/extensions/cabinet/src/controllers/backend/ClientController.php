<?php

namespace krok\cabinet\controllers\backend;

use krok\cabinet\interfaces\LoginAsInterface;
use krok\cabinet\models\Client;
use krok\cabinet\models\ClientSearch;
use krok\system\components\backend\Controller;
use Yii;
use yii\filters\VerbFilter;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
{
    /**
     * @inheritdoc
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
        ];
    }

    /**
     * Lists all Client models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch(Yii::$app->getRequest());
        $dataProvider = $searchModel->search();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();
        $model->setScenario($model::SCENARIO_CREATE);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws MethodNotAllowedHttpException
     */
    public function actionLoginAs($id)
    {
        $model = $this->findModel($id);

        if ($model instanceof LoginAsInterface) {
            return $this->redirect($model->loginAs());
        }

        throw new MethodNotAllowedHttpException('Method not allowed.');
    }

    /**
     * @param integer $id
     *
     * @return \yii\web\Response
     */
    public function actionRefreshToken($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_REFRESH_TOKEN);
        $model->save();

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
