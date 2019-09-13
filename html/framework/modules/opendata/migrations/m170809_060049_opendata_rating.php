<?php

use yii\db\Migration;

class m170809_060049_opendata_rating extends Migration
{
    public function safeUp()
    {
        $options = ($this->db->getDriverName() === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : null;

        $this->createTable(
            '{{%opendata_rating}}',
            [
                'id' => $this->primaryKey(),
                'passport_id' => $this->integer()->null()->defaultValue(null),
                'count' => $this->integer(),
                'rating' => $this->float(2),
            ],
            $options
        );
        $this->createIndex('passport_id', '{{%opendata_rating}}', ['passport_id']);

        $this->addForeignKey(
            'fk-od_rating_passport_id-od_passport_id',
            '{{%opendata_rating}}',
            'passport_id',
            '{{%opendata_passport}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-od_rating_passport_id-od_passport_id', '{{%opendata_rating}}');
        $this->dropTable('{{%opendata_rating}}');
    }
}
