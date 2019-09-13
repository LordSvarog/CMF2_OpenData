<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 01.08.17
 * Time: 10:04
 */

namespace app\modules\opendata\export\passport;

use Yii;
use yii\web\Response;

class ExportPassportSchemaJson implements ExportPassportSchemaInterface
{

    /**
     * @return string
     */
    public function render(): string
    {
        return file_get_contents(Yii::getAlias('@app/modules/opendata/data/meta-schema.json'));
    }

    /**
     * @return string
     */
    public function getResponseFormat(): string
    {
        return Response::FORMAT_JSON;
    }
}