<?php

namespace app\modules\opendata\controllers\backend;

use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataPassportSearch;
use app\modules\opendata\models\OpendataSet;
use krok\system\components\backend\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * PassportController implements the CRUD actions for OpendataPassport model.
 */
class PassportController extends Controller
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
     * Lists all OpendataPassport models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OpendataPassportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OpendataPassport model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $setsDataProvider = new ActiveDataProvider([
            'query' => OpendataSet::find()->where(['passport_id' => $id]),
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'setsDataProvider' => $setsDataProvider,
        ]);
    }

    /**
     * Creates a new OpendataPassport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OpendataPassport([
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
            'hidden' => OpendataPassport::HIDDEN_YES,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OpendataPassport model.
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
     * Deletes an existing OpendataPassport model.
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
     * Finds the OpendataPassport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return OpendataPassport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OpendataPassport::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
