<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:43
 */

namespace app\modules\opendata\export\data;

use Yii;
use yii\base\NotSupportedException;

/**
 * Class ExportDataFactory
 *
 * @package app\modules\opendata\export\data
 */
class ExportDataFactory implements ExportDataFactoryInterface
{
    /**
     * @param $format
     *
     * @return ExportDataInterface
     * @throws NotSupportedException
     */
    public function create($format): ExportDataInterface
    {
        switch ($format) {
            case 'xml':
                /** @var ExportDataXml $object */
                $object = Yii::createObject(ExportDataXml::class);
                break;
            case 'json':
                /** @var ExportDataJson $object */
                $object = Yii::createObject(ExportDataJson::class);
                break;
            case 'csv':
                /** @var ExportDataCsv $object */
                $object = Yii::createObject(ExportDataCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }

    /**
     * @param $format
     *
     * @return ExportDataSchemaInterface
     * @throws NotSupportedException
     */
    public function createSchema($format): ExportDataSchemaInterface
    {
        switch ($format) {
            case 'xsd':
                /** @var ExportDataSchemaXml $object */
                $object = Yii::createObject(ExportDataSchemaXml::class);
                break;
            case 'json':
                /** @var ExportDataSchemaJson $object */
                $object = Yii::createObject(ExportDataSchemaJson::class);
                break;
            case 'csv':
                /** @var ExportDataSchemaCsv $object */
                $object = Yii::createObject(ExportDataSchemaCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }
}