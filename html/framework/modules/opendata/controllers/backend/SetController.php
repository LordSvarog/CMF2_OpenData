<?php

namespace app\modules\opendata\controllers\backend;

use app\modules\opendata\dto\OpendataPropertyDTO;
use app\modules\opendata\dto\PassportSchemaDTO;
use app\modules\opendata\import\data\ImportDataFactoryInterface;
use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\models\OpendataSetProperty;
use app\modules\opendata\models\OpendataSetValue;
use app\modules\opendata\Module;
use app\modules\opendata\services\OpendataChartConfigService;
use krok\system\components\backend\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * SetController implements the CRUD actions for OpendataSet model.
 */
class SetController extends Controller
{
    /**
     * @var ImportDataFactoryInterface
     */
    protected $factory;

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
     * SetController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param ImportDataFactoryInterface $factory
     * @param array $config
     */
    public function __construct($id, Module $module, ImportDataFactoryInterface $factory, array $config = [])
    {
        $this->factory = $factory;
        parent::__construct($id, $module, $config);
    }

    /**
     * @param $id
     *
     * @return string|\yii\web\Response|string
     */
    public function actionCreate($id)
    {
        $model = new OpendataSet([
            'passport_id' => $id,
            'version' => date('Ymd'),
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->created_at = new Expression('NOW()');
            $model->updated_at = new Expression('NOW()');
            $model->save();

            // set passport dates
            $passport = OpendataPassport::findOne($id);
            $passport->updated_at = new Expression('NOW()');
            $passport->save();

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OpendataSet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $properties = OpendataSetProperty::find()->where([
            'set_id' => $model->id,
        ])->indexBy('id')->all();

        if (!$properties) {
            $properties = [
                'b1' => new OpendataSetProperty([
                    'id' => 0,
                    'passport_id' => $model->passport_id,
                    'set_id' => $model->id,
                    'name' => 'name',
                    'value' => 'Наименование',
                ]),
            ];
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (!empty(Yii::$app->request->post('OpendataSetProperty'))) {
                foreach (Yii::$app->request->post('OpendataSetProperty') as $prop) {

                    if (isset($prop['id']) && isset($properties[$prop['id']])) {
                        $properties[$prop['id']]->load($prop, '');
                        if ($properties[$prop['id']]->delete > 0) {
                            $properties[$prop['id']]->delete();
                        } else {
                            $properties[$prop['id']]->save();
                        }
                    } else {
                        $property = new OpendataSetProperty([
                            'passport_id' => $model->passport_id,
                            'set_id' => $model->id,
                        ]);
                        $property->load($prop, '');
                        $property->save();
                    }
                }
                // set passport dates
                $model->passport->updated_at = $model->updated_at;
                $model->passport->save();
            }
            $model->save();

            return $this->redirect(['/opendata/set/update', 'id' => $model->id]);

        } else {
            return $this->render('update', [
                'model' => $model,
                'properties' => $properties,
            ]);
        }
    }

    /**
     * Deletes an existing OpendataSet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        OpendataSetValue::deleteAll(['set_id' => $id]);
        OpendataSetProperty::deleteAll(['set_id' => $id]);
        $model->delete();

        Yii::$app->session->addFlash('success', 'Набор удален');

        return $this->redirect(['/opendata/passport/view/', 'id' => $model->passport_id]);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     */
    public function actionDeleteData($id)
    {
        $model = $this->findModel($id);
        OpendataSetValue::deleteAll(['set_id' => $id]);

        Yii::$app->session->addFlash('success', 'Данные набора удалены');

        return $this->redirect(['update', 'id' => $model->id]);
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionImport($id)
    {
        $model = $this->findModel($id);

        $import = Yii::$app->request->post('import');
        $delimiter = Yii::$app->request->post('delimiter');
        if ($import) {
            $factory = $this->factory->create('csv');

            $schemaDTO = new PassportSchemaDTO();
            $factory->setSchema($schemaDTO);

            foreach ($model->properties as $property) {
                $schemaDTO->addProperty(new OpendataPropertyDTO([
                    'id' => $property->id,
                    'title' => $property->title,
                    'format' => $property->type,
                    'name' => $property->name,
                ]));
            }

            $factory->setDelimiter($delimiter);
            try {
                $list = $factory->import($import);

                OpendataSetValue::deleteAll(['set_id' => $model->id]);

                foreach ($list as $dataDTO) {
                    if ($dataDTO->getProperties()) {
                        $modelValue = new OpendataSetValue([
                            'set_id' => $model->id,
                            'value' => $dataDTO->getValueAsArray(),
                        ]);
                        if (!$modelValue->save()) {
                            print_r($modelValue->getErrors());
                            exit;
                        }
                    }
                }
                Yii::$app->session->addFlash('success', 'Данные импортированы');
            } catch (\Exception $e) {
                Yii::$app->session->addFlash('danger', $e->getMessage());
            }

            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('import', ['model' => $model]);
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionData($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => OpendataSetValue::find()->where([
                'set_id' => $id,
            ]),
        ]);

        $properties = OpendataSetProperty::find()->where([
            'set_id' => $id,
        ])->all();

        return $this->render('data', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'properties' => $properties,
        ]);
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function actionChart($id)
    {
        $model = $this->findModel($id);

        if ($model->hasChart()) {
            $service = new OpendataChartConfigService([
                'model' => $model,
            ]);
            $chartConfig = $service->getConfig();

            return $this->render('chart', [
                'chartConfig' => $chartConfig,
            ]);
        } else {
            Yii::$app->session->addFlash('danger', 'Диаграмма не задана');

            return $this->redirect(['/opendata/set/update', 'id' => $model->id]);
        }
    }

    /**
     * Finds the OpendataSet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return OpendataSet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OpendataSet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
