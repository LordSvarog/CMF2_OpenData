<?php

use yii\db\Migration;

class m170801_045249_opendata_property_value extends Migration
{
    public function safeUp()
    {
        $options = ($this->db->getDriverName() === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : null;

        $this->createTable(
            '{{%opendata_set_value}}',
            [
                'id' => $this->primaryKey(),
                'set_id' => $this->integer()->null()->defaultValue(null),
                'value' => $this->text(),
            ],
            $options
        );
        $this->createIndex('set_id', '{{%opendata_set_value}}', ['set_id']);

        $this->addForeignKey(
            'fk-od_set_value_set_id-od_set_id',
            '{{%opendata_set_value}}',
            'set_id',
            '{{%opendata_set}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-od_set_value_set_id-od_set_id', '{{%opendata_set_value}}');
        $this->dropTable('{{%opendata_set_value}}');
    }

}
