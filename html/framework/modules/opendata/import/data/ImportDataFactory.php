<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:43
 */

namespace app\modules\opendata\import\data;

use Yii;
use yii\base\NotSupportedException;

/**
 * Class ImportDataFactory
 *
 * @package app\modules\opendata\import
 */
class ImportDataFactory implements ImportDataFactoryInterface
{
    /**
     * @param $format
     *
     * @return ImportDataInterface
     * @throws NotSupportedException
     */
    public function create(string $format): ImportDataInterface
    {
        switch ($format) {
            case 'csv':
                /** @var ImportDataInterface $object */
                $object = Yii::createObject(ImportDataCsv::class);
                break;

            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }
}