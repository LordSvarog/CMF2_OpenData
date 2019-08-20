<?php

namespace krok\auth\controllers\backend;

use krok\auth\models\Auth;
use krok\auth\models\AuthSearch;
use krok\system\components\backend\Controller;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * AuthController implements the CRUD actions for Auth model.
 */
class AuthController extends Controller
{
    /**
     * @inheritdoc
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
        ];
    }

    /**
     * Lists all Auth models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Auth model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'roles' => $this->getRoles(),
        ]);
    }

    /**
     * Creates a new Auth model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Auth();
        $model->setScenario(Auth::SCENARIO_CREATE);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'roles' => $this->getRoles(),
            ]);
        }
    }

    /**
     * Updates an existing Auth model.
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
                'roles' => $this->getRoles(),
            ]);
        }
    }

    /**
     * Deletes an existing Auth model.
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
     * @param integer $id
     *
     * @return \yii\web\Response
     */
    public function actionRefreshToken($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Auth::SCENARIO_REFRESH_TOKEN);
        $model->save();

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Auth model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Auth the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Auth::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return array
     */
    protected function getRoles(): array
    {
        return ArrayHelper::map(Yii::$app->getAuthManager()->getRoles(), 'name', 'description');
    }
}
