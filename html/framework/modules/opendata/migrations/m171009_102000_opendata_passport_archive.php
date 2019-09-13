<?php

use app\modules\opendata\models\OpendataPassport;
use yii\db\Migration;

class m171009_102000_opendata_passport_archive extends Migration
{
    public function safeUp()
    {
        $this->addColumn(OpendataPassport::tableName(), 'archive',
            $this->smallInteger(1)->notNull()->defaultValue(0)->after('hidden'));
    }

    public function safeDown()
    {
        $this->dropColumn(OpendataPassport::tableName(), 'archive');
        echo "m171009_102000_opendata_passport_archive - reverted.\n";
    }

}
