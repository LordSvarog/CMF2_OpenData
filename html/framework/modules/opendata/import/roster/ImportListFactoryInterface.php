<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 14:56
 */

namespace app\modules\opendata\import\roster;

interface ImportListFactoryInterface
{
    /**
     * @param string $format
     *
     * @return ImportListInterface
     */
    public function create(string $format): ImportListInterface;
}