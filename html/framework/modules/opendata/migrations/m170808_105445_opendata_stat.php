<?php

use yii\db\Migration;

class m170808_105445_opendata_stat extends Migration
{
    public function safeUp()
    {
        $options = ($this->db->getDriverName() === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : null;

        $this->createTable(
            '{{%opendata_stat}}',
            [
                'id' => $this->primaryKey(),
                'passport_id' => $this->integer()->null()->defaultValue(null),
                'set_id' => $this->integer()->null()->defaultValue(null),
                'format' => $this->string(15)->notNull()->defaultValue('html'),
                'count' => $this->integer(),
                'size' => $this->integer(),
            ],
            $options
        );
        $this->createIndex('format', '{{%opendata_stat}}', ['format']);
        $this->createIndex('passport_id', '{{%opendata_stat}}', ['passport_id']);
        $this->createIndex('set_id', '{{%opendata_stat}}', ['set_id']);

        $this->addForeignKey(
            'fk-od_stat_passport_id-od_passport_id',
            '{{%opendata_stat}}',
            'passport_id',
            '{{%opendata_passport}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-od_stat_set_id-od_set_id',
            '{{%opendata_stat}}',
            'set_id',
            '{{%opendata_set}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-od_stat_set_id-od_set_id', '{{%opendata_stat}}');
        $this->dropForeignKey('fk-od_stat_passport_id-od_passport_id', '{{%opendata_stat}}');
        $this->dropTable('{{%opendata_stat}}');
    }
}
