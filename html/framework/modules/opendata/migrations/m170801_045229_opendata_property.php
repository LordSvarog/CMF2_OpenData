<?php

use yii\db\Migration;

class m170801_045229_opendata_property extends Migration
{
    public function safeUp()
    {
        $options = ($this->db->getDriverName() === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : null;

        $this->createTable(
            '{{%opendata_set_property}}',
            [
                'id' => $this->primaryKey(),
                'passport_id' => $this->integer()->null()->defaultValue(null),
                'set_id' => $this->integer()->null()->defaultValue(null),
                'name' => $this->string(127)->notNull(),
                'title' => $this->string(512)->notNull(),
                'type' => $this->string(127)->notNull()->defaultValue(''),
                'created_at' => $this->dateTime()->null()->defaultValue(null),
                'updated_at' => $this->dateTime()->null()->defaultValue(null),
            ],
            $options
        );

        $this->createIndex('passport_id', '{{%opendata_set_property}}', ['passport_id']);
        $this->createIndex('set_id', '{{%opendata_set_property}}', ['set_id']);

        $this->addForeignKey(
            'fk-od_set_property_passport_id-od_passport_id',
            '{{%opendata_set_property}}',
            'passport_id',
            '{{%opendata_passport}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-od_set_property_set_id-od_set_id',
            '{{%opendata_set_property}}',
            'set_id',
            '{{%opendata_set}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-od_set_property_set_id-od_set_id', '{{%opendata_set_property}}');
        $this->dropForeignKey('fk-od_set_passport_id-od_passport_id', '{{%opendata_set_property}}');
        $this->dropTable('{{%opendata_set_property}}');
    }
}
