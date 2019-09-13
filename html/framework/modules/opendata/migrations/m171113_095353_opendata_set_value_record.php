<?php

use yii\db\Migration;

class m171113_095353_opendata_set_value_record extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%opendata_set_value}}', 'model', $this->string()->null()->defaultValue(null));
        $this->addColumn('{{%opendata_set_value}}', 'record_id', $this->integer()->null()->defaultValue(null));

        $this->createIndex('i-model-record_id', '{{%opendata_set_value}}', ['model', 'record_id'], true);
    }

    public function safeDown()
    {
        $this->dropIndex('i-model-record_id', '{{%opendata_set_value}}');

        $this->dropColumn('{{%opendata_set_value}}', 'model');
        $this->dropColumn('{{%opendata_set_value}}', 'record_id');
    }

}
