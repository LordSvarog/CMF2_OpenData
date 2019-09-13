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

class ExportListSchemaCsv implements ExportListSchemaInterface
{

    /**
     * @return string
     */
    public function render(): string
    {
        // no schema
        return '';
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        Yii::$app->response->getHeaders()->set('Content-Type', 'text/csv; charset=UTF-8');

        return Response::FORMAT_RAW;
    }
}