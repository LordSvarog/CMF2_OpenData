<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:33
 */

namespace app\modules\opendata\import\data;

use app\modules\opendata\dto\OpendataDataDTO;
use app\modules\opendata\dto\OpendataPropertyDTO;
use app\modules\opendata\dto\PassportSchemaDTO;
use app\modules\opendata\Module;
use Yii;
use yii\base\Exception;

class ImportDataMintrudCsv implements ImportDataInterface
{
    /**
     * @var Module
     */
    protected $module;

    /**
     * @var PassportSchemaDTO
     */
    protected $schema;

    /**
     * @var string
     */
    protected $delimiter = ';';

    /**
     * @param string $data
     *
     * @return OpendataDataDTO[]|array
     * @throws Exception
     */
    public function import(string $data): array
    {
        $this->module = Yii::$app->getModule('opendata');
        if ($this->module->importCharset != 'utf-8' && $encoding = mb_detect_encoding($data)) {
            $data = mb_convert_encoding($data, 'utf-8', $encoding);
        }
        $list = [];
        if (!$this->schema) {
            throw new Exception('Passport schema is not loaded');
        }

        // first row - properties name
        $columnToProperty = [];
        $rows = explode("\n", $data);

        foreach ($rows as $key => $row) {
            if (!preg_match('#^([\w\"\,\;\s\d\-\_\r\n]+)$#', $row)) {
                // try second row
                unset($rows[$key]);
                $key++;
                $row = isset($rows[$key]) ? $rows[$key] : '~';
                if (!preg_match('#^([\w\"\,\;\s\d\-\_\r\n]+)$#', $row)) {
                    throw new Exception('Wrong properties name in data: ' . $row . '   ' . __LINE__);
                }
            }
            unset($rows[$key]);
            $parts = explode($this->delimiter, $row);

            if (count($parts) <= 2) {
                $this->delimiter = ($this->delimiter == ';' ? ',' : ';');
                $parts = explode($this->delimiter, $row);
            }
            if (count($parts) <= 2) {
                throw new Exception('Can\'t detect delimiter for: ' . $row);
            }
            foreach ($parts as $part) {
                array_push($columnToProperty, $this->trim($part));
            }
            if (empty($columnToProperty)) {
                throw new Exception('Wrong properties name in data: ' . $row . '   ' . __LINE__);
            }
            break;
        }

        $data = implode("\n", $rows);
        $data = preg_replace("#\"([\r\n]+)#", '"' . $this->delimiter . ' ', $data);
        $data = preg_replace("#" . $this->delimiter . "([\r\n]+)#", $this->delimiter . $this->delimiter, $data);
        $data = preg_replace("#([\r\n]+)#", ' ', $data);
        $data = preg_replace(
            '#' . $this->delimiter . '([\"]+)([^"' . $this->delimiter . ']+)' . $this->delimiter . '#',
            $this->delimiter . '$1$2{dl}',
            $data);
        $parts = explode($this->delimiter, $data);
        $rows = array_chunk($parts, count($columnToProperty));

        foreach ($rows as $key => $row) {
            $dto = new OpendataDataDTO();
            foreach ($row as $key2 => $part) {
                $dto->setPropertyValue($columnToProperty[$key2], $this->trim($part));
            }
            array_push($list, $dto);
        }

        return $list;
    }

    /**
     * @param string $data
     *
     * @return PassportSchemaDTO
     */
    public function importSchema(string $data): PassportSchemaDTO
    {
        $this->module = Yii::$app->getModule('opendata');
        $rows = explode("\n", $data);
        $this->schema = new PassportSchemaDTO();
        if (count($rows) > 0) {
            foreach ($rows as $key => $row) {
                if ($key > 0) {
                    $parts = explode($this->module->importCsvDelimiter, $row);
                    if (count($parts) <= 2) {
                        $this->delimiter = ($this->delimiter == ';' ? ',' : ';');
                        $parts = explode($this->delimiter, $row);
                    }
                    if (count($parts) == 4 && preg_match('#^([\d]+)$#', $parts[0])) {
                        if (preg_match("#([a-z]+)#", $parts[2])) {
                            $this->schema->addProperty(new OpendataPropertyDTO(
                                    [
                                        'name' => $this->trim($parts[2]),
                                        'title' => $this->trim($parts[1]),
                                        'format' => preg_replace('#xsd?:#i', '', $this->trim($parts[3])),
                                    ]
                                )
                            );
                        } else {
                            $this->schema->addProperty(new OpendataPropertyDTO(
                                    [
                                        'name' => $this->trim($parts[1]),
                                        'title' => $this->trim($parts[2]),
                                        'format' => preg_replace('#xsd?:#i', '', $this->trim($parts[3])),
                                    ]
                                )
                            );
                        }
                    }
                }
            }
        }

        return $this->schema;
    }

    /**
     * @param PassportSchemaDTO $schema
     *
     * @return PassportSchemaDTO
     */
    public function setSchema(PassportSchemaDTO $schema)
    {
        return $this->schema = $schema;
    }

    /**
     * @return PassportSchemaDTO
     */
    public function getSchema(): PassportSchemaDTO
    {
        return $this->schema;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @param $str
     *
     * @return string
     */
    protected function trim($str)
    {
        $str = preg_replace('#\{dl\}#', $this->delimiter, $str);
        $str = preg_replace('#([\\\"]+)#', '"', $str);

        return trim($str, "\r\n\t\0\x0B\"");
    }
}