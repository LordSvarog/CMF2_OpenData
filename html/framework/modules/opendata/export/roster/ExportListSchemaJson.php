<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\roster;

use Yii;
use yii\web\Response;

class ExportListSchemaJson implements ExportListSchemaInterface
{

    /**
     * @return string
     */
    public function render(): string
    {
        return file_get_contents(Yii::getAlias('@app/modules/opendata/data/list-schema.json'));
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        return Response::FORMAT_JSON;
    }
}