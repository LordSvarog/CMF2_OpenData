<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:43
 */

namespace app\modules\opendata\export\passport;

use Yii;
use yii\base\NotSupportedException;

/**
 * Class ExportPassportFactory
 *
 * @package app\modules\opendata\export\passport
 */
class ExportPassportFactory implements ExportPassportFactoryInterface
{
    /**
     * @param $format
     *
     * @return ExportPassportInterface
     * @throws NotSupportedException
     */
    public function create($format): ExportPassportInterface
    {
        switch ($format) {
            case 'xml':
                /** @var ExportPassportXml $object */
                $object = Yii::createObject(ExportPassportXml::class);
                break;
            case 'json':
                /** @var ExportPassportJson $object */
                $object = Yii::createObject(ExportPassportJson::class);
                break;
            case 'csv':
                /** @var ExportPassportCsv $object */
                $object = Yii::createObject(ExportPassportCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }

    /**
     * @param $format
     *
     * @return ExportPassportSchemaInterface
     * @throws NotSupportedException
     */
    public function createSchema($format): ExportPassportSchemaInterface
    {
        switch ($format) {
            case 'xsd':
                /** @var ExportPassportSchemaXml $object */
                $object = Yii::createObject(ExportPassportSchemaXml::class);
                break;
            case 'json':
                /** @var ExportPassportSchemaJson $object */
                $object = Yii::createObject(ExportPassportSchemaJson::class);
                break;
            case 'csv':
                /** @var ExportPassportSchemaCsv $object */
                $object = Yii::createObject(ExportPassportSchemaCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }
}