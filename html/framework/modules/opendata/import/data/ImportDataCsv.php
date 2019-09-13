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

/**
 * Class ImportDataCsv
 *
 * @package app\modules\opendata\import\data
 */
class ImportDataCsv implements ImportDataInterface
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
    protected $delimiter;

    /**
     * @param string $data
     *
     * @return OpendataDataDTO[]|array
     * @throws Exception
     */
    public function import(string $data): array
    {
        $this->module = Yii::$app->getModule('opendata');
        $list = [];
        if (!$this->delimiter) {
            $this->delimiter = $this->module->importCsvDelimiter;
        }
        if (!$this->schema) {
            throw new Exception('Passport schema is not loaded');
        }

        $fh = fopen($this->module->tmpDir . '/import.csv', 'w+');
        fwrite($fh, $data);
        fclose($fh);

        $fh = fopen($this->module->tmpDir . '/import.csv', 'r');

        $columnToProperty = [];
        while ($row = fgetcsv($fh, 0, $this->delimiter)) {
            if (empty($columnToProperty)) {
                // first row - properties name
                if (count($row) <= 1) {
                    throw new Exception('Wrong delimiter for row ' . current($row));
                }
                foreach ($row as $key => $property) {
                    $property = $this->trimProperty($property);
                    if ($this->schema->getProperty($property)) {
                        $columnToProperty[$key] = $this->schema->getProperty($property)->getName();
                    }
                }
                if (empty($columnToProperty)) {
                    throw new Exception('Can\'t detect properties in row ' . current($row));
                }
            } else {
                $dto = new OpendataDataDTO();
                foreach ($row as $key => $value) {
                    if (isset($columnToProperty[$key])) {
                        $dto->setPropertyValue($columnToProperty[$key], $this->trim($value));
                    }
                }
                array_push($list, $dto);
            }
        }
        fclose($fh);
        @unlink($this->module->tmpDir . '/import.csv');

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
        if (!$this->delimiter) {
            $this->delimiter = $this->module->importCsvDelimiter;
        }
        if (count($rows) > 0) {
            foreach ($rows as $key => $row) {
                if ($key > 0) {
                    $parts = explode($this->delimiter, $row);
                    if (count($parts) == 3) {
                        $this->schema->addProperty(new OpendataPropertyDTO(
                                [
                                    'name' => $this->trim($parts[0]),
                                    'title' => $this->trim($parts[1]),
                                    'format' => $this->trim($parts[2]),
                                ]
                            )
                        );
                    } elseif (count($parts) == 4) {
                        $this->schema->addProperty(new OpendataPropertyDTO(
                                [
                                    'name' => $this->trim($parts[0]),
                                    'title' => $this->trim($parts[2]),
                                    'format' => $this->trim($parts[3]),
                                ]
                            )
                        );
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
     * @param $str
     *
     * @return string
     */
    protected function trim($str)
    {
        return trim($str, "\r\n\t\0\x0B\"");
    }

    /**
     * @param $str
     *
     * @return string
     */
    protected function trimProperty($str)
    {
        return preg_replace('#([^a-z\_]+)#i', '', $str);
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }
}
