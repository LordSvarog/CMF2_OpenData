<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:33
 */

namespace app\modules\opendata\import\passport;

use app\modules\opendata\dto\OpendataPassportDTO;
use app\modules\opendata\Module;
use Yii;

/**
 * Class ImportPassportCsv
 *
 * @package app\modules\opendata\import\passport
 */
class ImportPassportCsv implements ImportPassportInterface
{
    /**
     * @var Module
     */
    protected $module;

    /**
     * @param $data string
     *
     * @return OpendataPassportDTO
     */
    public function import($data): OpendataPassportDTO
    {
        $this->module = Yii::$app->getModule('opendata');
        $rows = explode("\n", $data);
        $dto = new OpendataPassportDTO();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $parts = explode($this->module->importCsvDelimiter, $row);
                if (count($parts) == 3) {
                    $num = $parts[0];
                    $value = trim($parts[2]);
                    $value = trim($value, '"');
                    switch ($num) {
                        case '1':
                            $code = preg_replace('#([\d]+)\-#', '', $value);
                            $dto->setCode($code);
                            break;
                        case '2':
                            $dto->setTitle($value);
                            break;
                        case '3':
                            $dto->setDescription($value);
                            break;
                        case '4':
                            $dto->setOwner($value);
                            break;
                        case '5':
                            $dto->setPublisherName($value);
                            break;
                        case '6':
                            $dto->setPublisherPhone($value);
                            break;
                        case '7':
                            $dto->setPublisherEmail($value);
                            break;
                        case '8':
                            $dto->setUrl($value);
                            break;
                        case '10':
                            $dto->setSchemaUrl($value);
                            break;
                        case '11':
                            $dto->setCreatedAt($value);
                            break;
                        case '12':
                            $dto->setUpdatedAt($value);
                            break;
                        case '13':
                            $dto->setChanges($value);
                            break;
                        case '14':
                            $dto->setUpdateFrequency($value);
                            break;
                        case '15':
                            $dto->setSubject($value);
                            break;
                    }
                }
            }
        }

        return $dto;
    }
}