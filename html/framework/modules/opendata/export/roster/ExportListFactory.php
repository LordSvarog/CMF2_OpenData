<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:43
 */

namespace app\modules\opendata\export\roster;

use Yii;
use yii\base\NotSupportedException;

/**
 * Class ExportListFactory
 *
 * @package app\modules\opendata\export\roster
 */
class ExportListFactory implements ExportListFactoryInterface
{
    /**
     * @param $format
     *
     * @return ExportListInterface
     * @throws NotSupportedException
     */
    public function create($format): ExportListInterface
    {
        switch ($format) {
            case 'xml':
                /** @var ExportListXml $object */
                $object = Yii::createObject(ExportListXml::class);
                break;
            case 'json':
                /** @var ExportListJson $object */
                $object = Yii::createObject(ExportListJson::class);
                break;
            case 'csv':
                /** @var ExportListCsv $object */
                $object = Yii::createObject(ExportListCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }

    /**
     * @param $format
     *
     * @return ExportListSchemaInterface
     * @throws NotSupportedException
     */
    public function createSchema($format): ExportListSchemaInterface
    {
        switch ($format) {
            case 'xsd':
                /** @var ExportListSchemaXml $object */
                $object = Yii::createObject(ExportListSchemaXml::class);
                break;
            case 'json':
                /** @var ExportListSchemaJson $object */
                $object = Yii::createObject(ExportListSchemaJson::class);
                break;
            case 'csv':
                /** @var ExportListSchemaCsv $object */
                $object = Yii::createObject(ExportListSchemaCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }
}