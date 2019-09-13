<?php

namespace app\modules\opendata;

use krok\system\components\backend\NameInterface;
use Yii;
use yii\base\Exception;

/**
 * page module definition class
 */
class Module extends \yii\base\Module implements NameInterface
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = null;

    /**
     * Available now: csv, xml, json
     *
     * @var array
     */
    public $exportFormats = ['xml', 'csv', 'json'];

    /**
     * Available now: csv, xsd, json
     *
     * @var array
     */
    public $exportSchemaFormats = ['xsd', 'csv', 'json'];

    /**
     * @var string
     */
    public $importUrl = '';

    /**
     * @var string
     */
    public $importCsvDelimiter = ';';

    /**
     * @var string
     */
    public $importCharset = 'utf-8';

    /**
     * @var string
     */
    public $inn = '';

    /**
     * @var string
     */
    public $tmpDir;

    /**
     * @var string
     */
    public $email;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->inn)) {
            throw new Exception('Option "inn" must be set');
        }
        if (!$this->tmpDir) {
            $this->tmpDir = dirname(__FILE__) . '/data';
        }
        parent::init();
    }

    /**
     * @return string
     */
    public static function getName()
    {
        return Yii::t('system', 'Opendata');
    }
}
