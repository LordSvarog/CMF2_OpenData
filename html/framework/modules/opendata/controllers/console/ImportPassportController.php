<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:19
 */

namespace app\modules\opendata\controllers\console;

use app\modules\opendata\import\passport\ImportPassportFactoryInterface;
use app\modules\opendata\import\roster\ImportListFactoryInterface;
use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\Module;
use yii\base\Exception;
use yii\console\Controller;
use yii\httpclient\Client;

class ImportPassportController extends Controller
{
    /**
     * @var int
     */
    public $interval = 200000;

    /**
     * @var array
     */
    protected $list = [];

    /**
     * @var Module
     */
    public $module;

    /**
     * @var ImportPassportFactoryInterface
     */
    protected $factory;

    /**
     * @var ImportListFactoryInterface
     */
    protected $listFactory;

    /**
     * ImportPassportController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param ImportListFactoryInterface $listFactory
     * @param ImportPassportFactoryInterface $factory
     * @param array $config
     */
    public function __construct(
        $id,
        Module $module,
        ImportListFactoryInterface $listFactory,
        ImportPassportFactoryInterface $factory,
        array $config = []
    ) {
        $this->module = $module;
        $this->listFactory = $listFactory;
        $this->factory = $factory;
        parent::__construct($id, $module, $config);
    }

    /**
     * @throws Exception
     */
    public function actionIndex()
    {

        if (empty($this->module->importUrl)) {
            throw new Exception('Import url is not set');
        }

        $client = new Client();
        $listData = $client->get($this->module->importUrl)->send();

        if (!($list = $listData->getContent())) {
            throw new Exception('No data for import');
        }
        $this->log('list of passports is received.');

        // import format - csv, xml, json
        $format = strtolower(pathinfo($this->module->importUrl, PATHINFO_EXTENSION));
        if ($format == 'csv'  && !mb_detect_encoding($list, 'UTF-8', true)) {
            $list = mb_convert_encoding($list, 'utf-8', $this->module->importCharset);
        }

        $passportList = $this->listFactory->create($format)
            ->import($list);

        if ($passportList) {

            foreach ($passportList as $passport) {
                if (empty($passport->getUrl())) {
                    continue;
                }
                usleep($this->interval);
                $this->log('Try parse data from passport: ' . pathinfo($passport->getUrl(), PATHINFO_FILENAME));
                $passportData = $client->get($passport->getUrl())->send();
                if (!($data = $passportData->getContent())) {
                    print 'No data for import passport: ' . $passport->getUrl() . PHP_EOL;
                    continue;
                }
                // import format - csv, xml, json
                $format = strtolower(pathinfo($passport->getUrl(), PATHINFO_EXTENSION));
                if ($format == 'csv' && !mb_detect_encoding($data, 'UTF-8', true)) {
                    $data = mb_convert_encoding($data, 'utf-8', $this->module->importCharset);
                }
                $dto = $this->factory->create($format)
                    ->import($data);

                $this->log('Data for passport "' . $dto->getCode() . '" is received');

                $model = OpendataPassport::findOne([
                    'code' => $dto->getCode(),
                ]);
                if (!$model) {
                    $model = new OpendataPassport([
                        'code' => $dto->getCode(),
                    ]);
                }

                $model->setAttributes([
                    'title' => $dto->getTitle(),
                    'description' => $dto->getDescription(),
                    'subject' => $dto->getSubject(),
                    'owner' => $dto->getOwner(),
                    'publisher_name' => $dto->getPublisherName(),
                    'publisher_email' => $dto->getPublisherEmail(),
                    'publisher_phone' => $dto->getPublisherPhone(),
                    'update_frequency' => $dto->getUpdateFrequency(),
                    'import_data_url' => $dto->getUrl(),
                    'import_schema_url' => $dto->getSchemaUrl(),
                    'hidden' => OpendataPassport::HIDDEN_YES,
                    'created_at' => $dto->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $dto->getUpdatedAt()->format('Y-m-d H:i:s'),
                ]);
                if ($model->validate()) {
                    $model->save(false);
                    if ($model->isNewRecord) {

                        $this->log('Passport created');
                    } else {
                        $this->log('Passport with id "' . $model->id . '" updated');
                    }
                    $modelSet = OpendataSet::findOne([
                        'passport_id' => $model->id,
                        'version' => $dto->getCreatedAt()->format('Ymd'),
                    ]);
                    if (!$modelSet) {
                        $modelSet = new OpendataSet([
                            'passport_id' => $model->id,
                            'version' => $dto->getCreatedAt()->format('Ymd'),
                            'hidden' => OpendataSet::HIDDEN_NO,
                        ]);
                    }
                    $modelSet->setAttributes([
                        'title' => 'Версия 1',
                        'changes' => $dto->getChanges(),
                        'created_at' => $dto->getCreatedAt()->format('Y-m-d H:i:s'),
                        'updated_at' => $dto->getUpdatedAt()->format('Y-m-d H:i:s'),
                    ]);
                    $modelSet->save();
                } else {
                    print_r($model->getErrors());
                    exit;
                }

            }
        }
    }

    /**
     * @param $message string
     */
    protected function log($message)
    {
        if (YII_ENV_DEV) {
            print $message . PHP_EOL;
        }
    }
}