<?php

use yii\db\Migration;

class m170801_043450_opendata_set extends Migration
{
    public function safeUp()
    {
        $options = ($this->db->getDriverName() === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : null;

        $this->createTable(
            '{{%opendata_set}}',
            [
                'id' => $this->primaryKey(),
                'passport_id' => $this->integer()->null()->defaultValue(null),
                'title' => $this->string(512)->notNull(),
                'version' => $this->string(127)->notNull(),
                'changes' => $this->string(127)->notNull(),
                'hidden' => $this->smallInteger(1)->notNull()->defaultValue(1),
                'created_at' => $this->dateTime()->null()->defaultValue(null),
                'updated_at' => $this->dateTime()->null()->defaultValue(null),
            ],
            $options
        );

        $this->createIndex('hidden', '{{%opendata_set}}', ['hidden']);
        $this->createIndex('passport_id', '{{%opendata_set}}', ['passport_id']);

        $this->addForeignKey(
            'fk-od_set_passport_id-od_passport_id',
            '{{%opendata_set}}',
            'passport_id',
            '{{%opendata_passport}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-od_set_passport_id-od_passport_id', '{{%opendata_set}}');
        $this->dropTable('{{%opendata_set}}');
    }
}
