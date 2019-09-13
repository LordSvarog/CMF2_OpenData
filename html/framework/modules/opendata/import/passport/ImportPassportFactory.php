<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:43
 */

namespace app\modules\opendata\import\passport;

use Yii;
use yii\base\NotSupportedException;

/**
 * Class ImportPassportFactory
 *
 * @package app\modules\opendata\import
 */
class ImportPassportFactory implements ImportPassportFactoryInterface
{
    /**
     * @param $format
     *
     * @return ImportPassportInterface
     * @throws NotSupportedException
     */
    public function create(string $format): ImportPassportInterface
    {
        switch ($format) {
            case 'csv':
                /** @var ImportPassportCsv $object */
                $object = Yii::createObject(ImportPassportCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }
}