<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 14:56
 */

namespace app\modules\opendata\import\passport;

/**
 * Interface ImportPassportFactoryInterface
 *
 * @package app\modules\opendata\import\passport
 */
interface ImportPassportFactoryInterface
{
    /**
     * @param string $format
     *
     * @return ImportPassportInterface
     */
    public function create(string $format): ImportPassportInterface;
}