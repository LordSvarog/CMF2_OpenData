<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:19
 */

namespace app\modules\opendata\controllers\console;

use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\import\data\ImportDataFactoryInterface;
use app\modules\opendata\import\data\ImportDataInterface;
use app\modules\opendata\import\passport\ImportPassportFactoryInterface;
use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\models\OpendataSetProperty;
use app\modules\opendata\models\OpendataSetValue;
use app\modules\opendata\Module;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\httpclient\Client;

class ImportDataController extends Controller
{
    /**
     * @var int
     */
    public $interval = 200000;
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var OpendataPassportDTO
     */
    protected $passportSchema;

    /**
     * @var Module
     */
    public $module;

    /**
     * @var ImportPassportFactoryInterface
     */
    protected $factory;

    /**
     * ImportPassportController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param ImportDataFactoryInterface $factory
     * @param array $config
     */
    public function __construct(
        $id,
        Module $module,
        ImportDataFactoryInterface $factory,
        array $config = []
    ) {
        ini_set('memory_limit', '256M');
        $this->module = $module;
        $this->factory = $factory;
        parent::__construct($id, $module, $config);
    }

    /**
     * @throws Exception
     */
    public function actionIndex()
    {

        $this->client = new Client();
        $passportList = OpendataPassport::find()->where([
            '>',
            'import_schema_url',
            '',
        ])->orderBy('id')->all();

        foreach ($passportList as $passport) {
            usleep($this->interval);
            $set = OpendataSet::findOne(['passport_id' => $passport->id]);
            if (!$set) {
                $this->log('Opendata set for passport ' . $passport->id . ' not found');
                continue;
            }
            if (!$data = $this->getFile($passport->import_schema_url)) {
                $passportSchemaData = $this->client->get($passport->import_schema_url)->send();

                if (!($data = $passportSchemaData->getContent())) {
                    throw new Exception('No schema data found for "' . $passport->code . '"');
                }
                $this->saveFile($passport->import_schema_url, $data);
            }
            $this->log('data schema is received from: ' . $passport->import_schema_url);

            // import format - csv, xml, json
            $format = strtolower(pathinfo($passport->import_schema_url, PATHINFO_EXTENSION));
            if ($format == 'csv' && !mb_detect_encoding($data, 'UTF-8', true)) {
                $data = mb_convert_encoding($data, 'utf-8', $this->module->importCharset);
            }

            try {
                $importData = $this->factory->create($format);
            } catch (Exception $e) {
                $this->log($e->getMessage());
                continue;
            }
            $dataSchema = $importData->importSchema($data);
            if ($dataSchema->getProperties()) {
                foreach ($dataSchema->getProperties() as $prop) {
                    $modelProp = OpendataSetProperty::findOne([
                        'passport_id' => $passport->id,
                        'set_id' => $set->id,
                        'name' => $prop->getName(),
                    ]);
                    if (!$modelProp) {
                        $modelProp = new OpendataSetProperty([
                            'passport_id' => $passport->id,
                            'set_id' => $set->id,
                            'name' => $prop->getName(),
                        ]);
                    }
                    $modelProp->setAttributes([
                        'title' => $prop->getTitle(),
                        'type' => $prop->getFormat(),
                    ]);
                    if ($modelProp->validate()) {
                        $modelProp->save(false);

                    } else {
                        print_r($modelProp->getErrors());
                        exit;
                    }
                }
                if (!$this->saveValues($set, $importData, $passport->import_data_url)) {
                    $this->log('Fail to import data from ' . $passport->import_data_url);
                }
            }
        }

    }

    /**
     * @param OpendataSet $model
     * @param ImportDataInterface $importData
     * @param $url
     *
     * @return bool
     * @throws Exception
     */
    protected function saveValues(OpendataSet $model, ImportDataInterface $importData, $url)
    {
        usleep($this->interval);
        $this->log('url for data import: ' . $url);
        if (!$data = $this->getFile($url)) {
            $valueData = $this->client->get($url)->send();

            if (!($data = $valueData->getContent())) {
                throw new Exception('No data found by url "' . $url . '"');
            }
            $this->saveFile($url, $data);
        }


        $this->log('list of data is received.');

        // import format - csv, xml, json
        $format = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        if ($this->module->importCharset != 'utf-8' && $format == 'csv') {
            $data = mb_convert_encoding($data, 'utf-8', $this->module->importCharset);
        }

        try {
            $list = $importData->import($data);
        } catch (Exception $e) {
            $this->log($e->getMessage());

            return false;
        }
        $ok = false;
        if ($list) {
            OpendataSetValue::deleteAll(['set_id' => $model->id]);
            foreach ($list as $value) {
                if ($value->getProperties()) {
                    $modelValue = new OpendataSetValue([
                        'set_id' => $model->id,
                        'value' => $value->getValueAsArray(),
                    ]);
                    if (!$modelValue->save()) {
                        print_r($modelValue->getErrors());
                        exit;
                    }
                    $ok = true;
                } else {
                    $this->log('Empty properties');
                }
            }

            return $ok;
        }

        return false;
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

    /**
     * @param $url string
     * @param $data string
     *
     * @return bool
     */
    protected function saveFile($url, $data)
    {
        if (YII_ENV_DEV) {
            $path = Yii::getAlias('@app/modules/opendata/data') . '/';
            $filename = pathinfo($url, PATHINFO_BASENAME);
            $fh = fopen($path . $filename, 'w+');
            fwrite($fh, $data);
            fclose($fh);

            return true;
        }

        return false;
    }

    /**
     * @param $url string
     *
     * @return bool|string
     */
    protected function getFile($url)
    {
        if (YII_ENV_DEV) {
            $path = Yii::getAlias('@app/modules/opendata/data') . '/';
            $filename = pathinfo($url, PATHINFO_BASENAME);
            if (file_exists($path . $filename)) {
                return file_get_contents($path . $filename);
            }
        }

        return false;
    }
}