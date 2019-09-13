<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:43
 */

namespace  app\modules\opendata\import\roster;

use Yii;
use yii\base\NotSupportedException;

/**
 * Class ImportListFactory
 *
 * @package app\modules\opendata\import
 */
class ImportListFactory implements ImportListFactoryInterface
{
    /**
     * @param $format
     *
     * @return ImportListInterface
     * @throws NotSupportedException
     */
    public function create(string $format): ImportListInterface
    {
        switch ($format) {
            case 'csv':
                /** @var ImportListInterface $object */
                $object = Yii::createObject(ImportListCsv::class);
                break;
            default:
                throw new NotSupportedException('Format ' . $format . ' is not supported');
        }

        return $object;
    }
}