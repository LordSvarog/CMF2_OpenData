<?php

use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\models\OpendataStat;
use yii\db\Migration;

class m170808_112319_opendata_stat_generate extends Migration
{
    public function safeUp()
    {
        $formats = [OpendataStat::FORMAT_SHOWS, OpendataStat::FORMAT_XML, OpendataStat::FORMAT_CSV, OpendataStat::FORMAT_JSON];
        $passportList = OpendataPassport::find()->all();
        $setList = OpendataSet::find()->all();
        foreach ($formats as $format) {
            // list stat
            $this->insert(OpendataStat::tableName(), [
                'format' => $format,
                'count' => mt_rand(1, 200),
                'size' => mt_rand(39, 42) * 1024,
            ]);
            // passport stat
            foreach ($passportList as $passport) {
                $this->insert(OpendataStat::tableName(), [
                    'passport_id' => $passport->id,
                    'format' => $format,
                    'count' => mt_rand(1, 200),
                    'size' => mt_rand(20, 45) * 1024,
                ]);
            }
            // set stat
            foreach ($setList as $set) {
                $this->insert(OpendataStat::tableName(), [
                    'set_id' => $set->id,
                    'format' => $format,
                    'count' => mt_rand(1, 200),
                    'size' => mt_rand(20, 45) * 1024,
                ]);
            }
        }

    }

    public function safeDown()
    {
        $this->truncateTable(OpendataStat::tableName());

        echo "m170808_112319_opendata_stat_generate - reverted.\n";
    }

}
