<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 03.08.17
 * Time: 10:50
 */

namespace app\modules\opendata\controllers\frontend;

use app\modules\opendata\dto\OpendataDataDTO;
use app\modules\opendata\dto\OpendataListDTO;
use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\dto\OpendataPropertyDTO;
use app\modules\opendata\dto\OpendataSetDTO;
use app\modules\opendata\dto\OpendataStatDto;
use app\modules\opendata\dto\PassportSchemaDTO;
use app\modules\opendata\export\data\ExportDataFactoryInterface;
use app\modules\opendata\export\passport\ExportPassportFactoryInterface;
use app\modules\opendata\export\roster\ExportListFactoryInterface;
use app\modules\opendata\forms\CommentForm;
use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataRating;
use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\models\OpendataSetProperty;
use app\modules\opendata\models\OpendataSetValue;
use app\modules\opendata\models\OpendataStat;
use app\modules\opendata\Module;
use app\modules\opendata\services\OpendataChartConfigService;
use app\modules\opendata\services\OpendataMapService;
use krok\cabinet\models\Client;
use Yii;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DefaultController extends Controller
{
    /**
     * @var string
     */
    public $layout = '@app/modules/opendata/views/layouts/index';

    /**
     * @var \app\modules\opendata\Module
     */
    public $module;

    /**
     * @var ExportListFactoryInterface
     */
    protected $listFactory;

    /**
     * @var ExportPassportFactoryInterface
     */
    protected $passportFactory;

    /**
     * @var ExportDataFactoryInterface
     */
    protected $dataFactory;

    /**
     * ImportPassportController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param ExportListFactoryInterface $listFactory
     * @param ExportPassportFactoryInterface $passportFactory
     * @param ExportDataFactoryInterface $dataFactory
     * @param array $config
     */
    public function __construct(
        $id,
        Module $module,
        ExportListFactoryInterface $listFactory,
        ExportPassportFactoryInterface $passportFactory,
        ExportDataFactoryInterface $dataFactory,
        array $config = []
    ) {
        $this->module = $module;
        $this->listFactory = $listFactory;
        $this->passportFactory = $passportFactory;
        $this->dataFactory = $dataFactory;

        parent::__construct($id, $module, $config);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'cmf2' : null,
                'testLimit' => 0,
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $formats = $this->module->exportFormats;

        $key = [
            __CLASS__,
            __METHOD__,
        ];

        $dependency = new TagDependency([
            'tags' => [
                OpendataPassport::class,
                OpendataSet::class,
            ],
        ]);

        if (($data = Yii::$app->cache->get($key)) === false) {
            $passportList = OpendataPassport::find()->where([
                OpendataPassport::tableName() . '.[[hidden]]' => OpendataPassport::HIDDEN_NO,
                OpendataSet::tableName() . '.[[hidden]]' => OpendataSet::HIDDEN_NO,
                OpendataPassport::tableName() . '.[[archive]]' => OpendataPassport::ARCHIVE_NO,
            ])->joinWith('set', true, 'INNER JOIN')
                ->all();

            $passportArchiveList = OpendataPassport::find()->where([
                OpendataPassport::tableName() . '.[[hidden]]' => OpendataPassport::HIDDEN_NO,
                OpendataSet::tableName() . '.[[hidden]]' => OpendataSet::HIDDEN_NO,
                OpendataPassport::tableName() . '.[[archive]]' => OpendataPassport::ARCHIVE_YES,
            ])->joinWith('set', true, 'INNER JOIN')
                ->all();

            $data = [$passportList, $passportArchiveList];

            Yii::$app->cache->set($key, $data, null, $dependency);
        }

        list($passportList, $passportArchiveList) = $data;

        $key = [
            'opendata.stat',
        ];

        if (($stat = Yii::$app->cache->get($key)) === false) {

            $shows = OpendataStat::find()->select(['count'])
                ->roster()
                ->format(OpendataStat::FORMAT_SHOWS)
                ->scalar();

            $downloads = OpendataStat::find()->select([
                new Expression('SUM(' . OpendataStat::tableName() . '.[[count]]) as [[count]]'),
            ])->roster()
                ->format($this->module->exportFormats)
                ->scalar();

            $rosterSizes = OpendataStat::find()->select([
                'size',
                'format',
            ])->roster()
                ->format($this->module->exportFormats)
                ->indexBy('format')
                ->column();

            // generate stat if not exists
            if (!$shows) {
                $stat = new OpendataStat([
                    'format' => OpendataStat::FORMAT_SHOWS,
                    'count' => 1,
                    'size' => 0,
                ]);
                $stat->save();

                foreach ([OpendataStat::FORMAT_JSON, OpendataStat::FORMAT_CSV, OpendataStat::FORMAT_XML] as $format) {
                    $stat = new OpendataStat([
                        'format' => $format,
                        'count' => 0,
                        'size' => 0,
                    ]);
                    $stat->save();
                }
                $shows = 1;
                $downloads = 0;
            }
            $rosterStat = new OpendataStatDto([
                'shows' => (int)$shows,
                'downloads' => (int)$downloads,
            ]);

            $passportShows = OpendataStat::find()->select(['count', 'passport_id'])->where([
                'NOT',
                ['passport_id' => null],
            ])->format(OpendataStat::FORMAT_SHOWS)
                ->indexBy('passport_id')
                ->column();

            $passportDownloads = OpendataStat::find()->select([
                new Expression('SUM(' . OpendataStat::tableName() . '.[[count]]) as [[count]]'),
                OpendataSet::tableName() . '.[[passport_id]]',
            ])->joinWith('set', false, 'INNER JOIN')
                ->format($this->module->exportFormats)
                ->indexBy('passport_id')
                ->groupBy(OpendataSet::tableName() . '.[[passport_id]]')
                ->column();

            $passportStat = [];

            foreach (ArrayHelper::merge($passportList, $passportArchiveList) as $model) {
                $passportStat[$model->id] = new OpendataStatDto([
                    'shows' => (int)ArrayHelper::getValue($passportShows, $model->id, 0),
                    'downloads' => (int)ArrayHelper::getValue($passportDownloads, $model->id, 0),
                ]);
            }

            //
            $stat = [$rosterStat, $rosterSizes, $passportStat];

            Yii::$app->cache->set($key, $stat, 600, $dependency);
        }

        list($rosterStat, $rosterSizes, $passportStat) = $stat;

        // update list counters
        OpendataStat::updateAllCounters(['count' => 1], [
            'format' => OpendataStat::FORMAT_SHOWS,
            'passport_id' => null,
            'set_id' => null,
        ]);

        $formModel = new CommentForm();
        // preload form attributes from authorized user
        if (!Yii::$app->user->getIsGuest()) {
            /** @var Client $user */
            $user = Yii::$app->user->getIdentity();
            $formModel->setAttributes([
                'email' => $user->login,
            ]);
        }

        return $this->render('index', [
            'formats' => $formats,
            'passportList' => $passportList,
            'passportArchiveList' => $passportArchiveList,
            'rosterStat' => $rosterStat,
            'rosterSizes' => $rosterSizes,
            'passportStat' => $passportStat,
            'formModel' => $formModel,
        ]);
    }

    /**
     * @param $ext
     *
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionList($ext)
    {
        if ($ext == 'html') {
            return $this->redirect(['index']);
        }
        if (in_array($ext, $this->module->exportFormats)) {

            $factory = $this->listFactory->create($ext);
            $passportList = OpendataPassport::find()->where([
                'hidden' => OpendataPassport::HIDDEN_NO,
            ])->all();

            $stat = OpendataStat::find()->format($ext)
                ->roster()->one();
            if (!$stat) {
                $stat = new OpendataStat([
                    'format' => $ext,
                    'count' => 0,
                    'size' => 0,
                ]);
            }
            foreach ($passportList as $passport) {
                foreach ($this->module->exportFormats as $format) {
                    $factory->addItem(new OpendataListDTO([
                        'identifier' => $this->module->inn . '-' . $passport->code,
                        'format' => $format,
                        'title' => $passport->title,
                        'url' => Url::to([
                            '/opendata/passport-meta',
                            'id' => $passport->id,
                            'ext' => $format,
                        ], true),
                    ]));
                }
            }
            Yii::$app->response->headers->add('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            Yii::$app->response->format = $factory->getResponseFormat();
            Yii::$app->response->content = $factory->render();
            // set stat
            $stat->setAttributes([
                'count' => $stat->count + 1,
                'size' => strlen(Yii::$app->response->content),
            ]);
            $stat->save();
            Yii::$app->response->send();
            Yii::$app->end();
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @param $ext
     *
     * @throws NotFoundHttpException
     */
    public function actionListSchema($ext)
    {
        if (in_array($ext, $this->module->exportSchemaFormats)) {

            $factory = $this->listFactory->createSchema($ext);

            Yii::$app->response->headers->add('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            Yii::$app->response->format = $factory->getResponseFormat();
            Yii::$app->response->content = $factory->render();
            Yii::$app->response->send();
            Yii::$app->end();
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPassport($id)
    {
        $model = OpendataPassport::find()
            ->with('opendataSets')
            ->where(['id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $shows = OpendataStat::find()->select([
            new Expression('SUM(' . OpendataStat::tableName() . '.[[count]]) as [[count]]'),
        ])->format(OpendataStat::FORMAT_SHOWS)
            ->andWhere([OpendataStat::tableName() . '.[[passport_id]]' => $model->id])
            ->scalar();

        $downloads = OpendataStat::find()->select([
            new Expression('SUM(' . OpendataStat::tableName() . '.[[count]]) as [[count]]'),
        ])->joinWith('set', false, 'INNER JOIN')
            ->format($this->module->exportFormats)
            ->andWhere([OpendataSet::tableName() . '.[[passport_id]]' => $model->id])
            ->scalar();


        $setStat = OpendataStat::find()->set($model->set->id)->indexBy('format')->all();

        $stat = OpendataStat::find()->format(OpendataStat::FORMAT_SHOWS)
            ->passport($id)->one();
        if (!$stat) {
            $stat = new OpendataStat([
                'passport_id' => $id,
                'format' => OpendataStat::FORMAT_SHOWS,
                'count' => 0,
                'size' => 0,
            ]);
            $stat->save();
        }

        $passportStat = new OpendataStatDto([
            'shows' => (int)$shows,
            'downloads' => (int)$downloads,
        ]);

        // update passport counters
        OpendataStat::updateAllCounters(['count' => 1], [
            'format' => OpendataStat::FORMAT_SHOWS,
            'passport_id' => $model->id,
            'set_id' => null,
        ]);

        $formModel = new CommentForm();
        // preload form attributes from authorized user
        if (!Yii::$app->user->getIsGuest()) {
            /** @var Client $user */
            $user = Yii::$app->user->getIdentity();
            $formModel->setAttributes([
                'email' => $user->login,
            ]);
        }

        $rating = OpendataRating::findOne(['passport_id' => $id]);

        $createdAt = OpendataSet::find()
            ->select('created_at')
            ->where(['passport_id' => $id])
            ->orderBy(['created_at' => SORT_ASC])
            ->limit(1)
            ->scalar();
        $updatedAt = OpendataSet::find()
            ->select('updated_at')
            ->where(['passport_id' => $id])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(1)
            ->scalar();


        return $this->render('passport', [
            'model' => $model,
            'inn' => $this->module->inn,
            'formats' => $this->module->exportFormats,
            'passportStat' => $passportStat,
            'setStat' => $setStat,
            'formModel' => $formModel,
            'rating' => $rating,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ]);
    }

    /**
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionSendComment()
    {
        $id = Yii::$app->request->get('id');
        $model = null;
        if ($id) {
            $model = OpendataPassport::findOne($id);

            if (!$model) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $formModel = new CommentForm();

        if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {

            Yii::$app
                ->getMailer()
                ->compose('@app/modules/opendata/mail/comment', [
                    'model' => $model,
                    'form' => $formModel,
                ])
                ->setSubject('Комментарий к открытым данным')
                ->setFrom(Yii::$app->params['email'])
                ->setTo($this->module->email)
                ->send();

            return ['success' => true];
        }

        return ['success' => false, 'errors' => $formModel->getErrors()];
    }

    /**
     * @param $id
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionRate($id)
    {
        $passport = OpendataPassport::findOne($id);
        if (!$passport) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = OpendataRating::findOne(['passport_id' => $id]);

        if (!$model) {
            $model = new OpendataRating([
                'scenario' => OpendataRating::SCENARIO_CREATE,
                'passport_id' => $id,
                'count' => 0,
                'rating' => 0,
            ]);
        } else {
            $model->setScenario(OpendataRating::SCENARIO_UPDATE);
        }

        $rates = [];
        if (Yii::$app->request->cookies->has('opendata_rating')) {
            $rates = Json::decode(Yii::$app->request->cookies->get('opendata_rating'));
        }
        if ($rates && isset($rates[$id])) {
            $model->previousRate = $rates[$id];
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            if ($rates) {
                $rates[$id] = $model->rate;
                Yii::$app->response->cookies->remove('opendata_rating');
            } else {
                $rates = [$id => $model->rate];
            }
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'opendata_rating',
                'value' => Json::encode($rates),
                'expire' => time() + 365 * 24 * 3600,
            ]));

            return ['success' => true, 'rating' => $model->rating, 'count' => $model->count];
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }

    /**
     * @param $id
     * @param $ext
     *
     * @throws NotFoundHttpException
     */
    public function actionPassportMeta($id, $ext)
    {
        $model = OpendataPassport::find()
            ->with('opendataSets')
            ->where(['id' => $id])
            ->one();

        if (!$model || !in_array($ext, $this->module->exportFormats)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $stat = OpendataStat::find()->format($ext)
            ->passport($id)->one();

        if (!$stat) {
            $stat = new OpendataStat([
                'passport_id' => $id,
                'format' => $ext,
                'count' => 0,
                'size' => 0,
            ]);
        }
        // create DTO objects
        $dto = new OpendataPassportDTO([
            'identifier' => $this->module->inn . '-' . $model->code,
            'schemaUrl' => Url::to([
                '/opendata/passport-meta',
                'code' => $model->code,
                'id' => $model->id,
                'ext' => ($ext != 'xml' ?: 'xsd'),
            ], true),
            'title' => $model->title,
            'code' => $model->code,
            'description' => $model->description,
            'owner' => $model->owner,
            'subject' => $model->subject,
            'publisherName' => $model->publisher_name,
            'publisherPhone' => $model->publisher_phone,
            'publisherEmail' => $model->publisher_email,
            'createdAt' => $model->created_at,
            'updatedAt' => $model->updated_at,
            'updatedFrequency' => $model->update_frequency,
        ]);

        foreach ($model->opendataSets as $set) {
            $dto->addSet(new OpendataSetDTO([
                'title' => $set->title,
                'changes' => $set->changes,
                'url' => Url::to([
                    '/opendata/data',
                    'code' => $model->code,
                    'version' => $set->version,
                    'data-time' => Yii::$app->formatter->asDate($set->updated_at, 'php:Ymd'),
                    'ext' => $ext,
                ], true),
                'structureUrl' => Url::to([
                    '/opendata/data-schema',
                    'code' => $model->code,
                    'version' => $set->version,
                    'ext' => ($ext == 'xml' ? 'xsd' : $ext),
                ], true),
                'version' => $set->version,
                'createdAt' => $set->created_at,
                'updatedAt' => $set->updated_at,
            ]));
        }

        $factory = $this->passportFactory
            ->create($ext);
        $factory->load($dto);

        Yii::$app->response->headers->add('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        Yii::$app->response->format = $factory->getResponseFormat();
        Yii::$app->response->content = $factory->render();

        // set stat
        $stat->setAttributes([
            'count' => $stat->count + 1,
            'size' => strlen(Yii::$app->response->content),
        ]);
        $stat->save();

        Yii::$app->response->send();
        Yii::$app->end();
    }

    /**
     * @param $id
     * @param $ext
     *
     * @throws NotFoundHttpException
     */
    public function actionPassportMetaSchema($id, $ext)
    {
        $model = OpendataPassport::find()
            ->with('opendataSets')
            ->where(['id' => $id])
            ->one();

        if (!$model || !in_array($ext, $this->module->exportSchemaFormats)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $factory = $this->passportFactory->createSchema($ext);

        Yii::$app->response->headers->add('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        Yii::$app->response->format = $factory->getResponseFormat();
        Yii::$app->response->content = $factory->render();
        Yii::$app->response->send();
        Yii::$app->end();

    }

    /**
     * @param $id
     * @param $version
     * @param $ext
     *
     * @throws NotFoundHttpException
     */
    public function actionData($id, $version, $ext)
    {
        $model = OpendataPassport::findOne($id);
        $set = OpendataSet::findOne([
            'passport_id' => $id,
            'version' => $version,
        ]);


        if (!$model || !$set || !in_array($ext, $this->module->exportFormats)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        /** @var OpendataSetProperty[] $properties */
        $properties = OpendataSetProperty::find()->where([
            'set_id' => $set->id,
        ])->all();

        if (empty($properties)) {
            // hide this set when no properties in this set
            $set->hidden = OpendataSet::HIDDEN_YES;
            $set->save();

            throw new NotFoundHttpException('The requested page does not exist.');
        }

        /** @var OpendataSetValue[] $data */
        $data = OpendataSetValue::find()->where([
            'set_id' => $set->id,
        ])->orderBy([
            'record_id' => SORT_DESC,
            'id' => SORT_ASC,
        ])->all();

        $stat = OpendataStat::find()->format($ext)
            ->set($set->id)->one();

        if (!$data) {
            $data = [
                new OpendataSetValue([
                    'value' => [],
                ]),
            ];
        }

        if (!$stat) {
            $stat = new OpendataStat([
                'set_id' => $set->id,
                'format' => $ext,
                'count' => 0,
                'size' => 0,
            ]);
        }
        $dto = new OpendataPassportDTO([
            'identifier' => $this->module->inn . '-' . $model->code,
            'code' => $model->code,
            'title' => $model->title,
            'description' => $model->description,
            'schemaUrl' => Url::to([
                '/opendata/data-schema',
                'ext' => ($ext == 'xml' ? 'xsd' : $ext),
                'version' => $set->version,
                'data-time' => $set->getVersionDate(),
                'id' => $model->id,
                'code' => $model->code,
            ], true),
        ]);

        $schemaDTO = new PassportSchemaDTO();
        foreach ($properties as $property) {
            $schemaDTO->addProperty(new OpendataPropertyDTO([
                'title' => $property->title,
                'name' => $property->name,
                'format' => $property->type,
            ]));
        }
        $factory = $this->dataFactory->create($ext);
        $factory->loadSchema($schemaDTO);
        $factory->loadPassport($dto);

        foreach ($data as $value) {
            $valueDto = new OpendataDataDTO();
            $valueDto->setBatchPropertiesValues($value->value);
            $factory->addItem($valueDto);
        }

        Yii::$app->response->headers->add('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        Yii::$app->response->format = $factory->getResponseFormat();
        Yii::$app->response->content = $factory->render();
        // set stat
        $stat->setAttributes([
            'count' => $stat->count + 1,
            'size' => strlen(Yii::$app->response->content),
        ]);
        $stat->save();
        Yii::$app->response->send();
        Yii::$app->end();
    }

    /**
     * @param $id
     * @param $version
     * @param $ext
     *
     * @throws NotFoundHttpException
     */
    public function actionDataSchema($id, $version, $ext)
    {
        $model = OpendataPassport::findOne($id);
        $set = OpendataSet::find()->where([
            OpendataSet::tableName() . '.[[passport_id]]' => $id,
            OpendataSet::tableName() . '.[[version]]' => $version,
        ])->joinWith('properties')
            ->one();

        if (!$model || !$set || !in_array($ext, $this->module->exportSchemaFormats)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $factory = $this->dataFactory->createSchema($ext);
        $dto = new OpendataPassportDTO([
            'identifier' => $this->module->inn . '-' . $model->code,
            'code' => $model->code,
            'title' => $model->title,
            'description' => $model->description,
        ]);

        $factory->loadPassport($dto);
        foreach ($set->properties as $property) {
            $propertyDto = new OpendataPropertyDTO([
                'name' => $property->name,
                'title' => $property->title,
                'format' => $property->type,
            ]);
            $factory->addProperty($propertyDto);
        }

        Yii::$app->response->headers->add('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        Yii::$app->response->format = $factory->getResponseFormat();
        Yii::$app->response->content = $factory->render();
        Yii::$app->response->send();
        Yii::$app->end();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionTerms()
    {
        $file = Yii::getAlias('@app/modules/opendata/data/terms_of_use.docx');
        if (!is_file($file)) {
            throw new NotFoundHttpException('Page not found.');
        }

        Yii::$app->response->sendFile($file, 'Условия использования открытых данных.docx')
            ->send();
    }

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSetChart($id)
    {
        $model = OpendataSet::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $service = new OpendataChartConfigService([
            'noTitle' => true,
            'model' => $model,
        ]);
        $chartConfig = $service->getConfig();

        return $this->render('set-chart', [
            'model' => $model,
            'chartConfig' => $chartConfig,
        ]);
    }

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSetData($id)
    {
        $model = OpendataSet::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $perPage = Yii::$app->request->get('per-page', OpendataSet::PER_PAGE_10);
        $perPage = $perPage < OpendataSet::PER_PAGE_10 ? OpendataSet::PER_PAGE_10 : $perPage;
        $dataProvider = new ActiveDataProvider([
            'query' => OpendataSetValue::find()->where([
                'set_id' => $id,
            ]),
            'pagination' => [
                'pageSize' => $perPage,
            ],
        ]);

        $properties = OpendataSetProperty::find()->where([
            'set_id' => $id,
        ])->all();

        return $this->render('set-data', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'properties' => $properties,
        ]);
    }

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSetAtlas($id)
    {
        $model = OpendataSet::findOne(['id' => $id]);

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $service = new OpendataMapService([
            'model' => $model,
        ]);
        $data = $service->getData();

        return $this->render('set-atlas', [
            'model' => $model,
            'data' => $data,
        ]);
    }
}