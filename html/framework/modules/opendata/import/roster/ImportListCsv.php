<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 11:33
 */

namespace app\modules\opendata\import\roster;

use app\modules\opendata\dto\OpendataListDTO;
use app\modules\opendata\Module;
use Yii;

/**
 * Class ImportListCsv
 *
 * @package app\modules\opendata\import\roster
 */
class ImportListCsv implements ImportListInterface
{
    /**
     * @var array
     */
    protected $list = [];

    /**
     * @var Module
     */
    protected $module;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @param string $data
     *
     * @return OpendataListDTO[]
     */
    public function import(string $data): array
    {
        $this->module = Yii::$app->getModule('opendata');
        $rows = explode("\n", $data);
        if (!$this->delimiter) {
            $this->delimiter = $this->module->importCsvDelimiter;
        }
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $parts = explode($this->delimiter, $row);
                if (count($parts) == 2) {
                    array_push($this->list,
                        new OpendataListDTO([
                            'title' => $parts[0],
                            'url' => $parts[1],
                        ])
                    );
                }
            }

            return $this->list;
        }

        return [];
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }
}