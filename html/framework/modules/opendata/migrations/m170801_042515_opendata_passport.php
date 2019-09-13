<?php

use yii\db\Migration;

class m170801_042515_opendata_passport extends Migration
{
    public function safeUp()
    {
        $options = ($this->db->getDriverName() === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci' : null;

        $this->createTable(
            '{{%opendata_passport}}',
            [
                'id' => $this->primaryKey(),
                'title' => $this->string(512)->notNull(),
                'code' => $this->string(127)->notNull(),
                'description' => $this->text(),
                'subject' => $this->string(512)->notNull()->defaultValue(''),
                'owner' => $this->string(255)->notNull(),
                'publisher_name' => $this->string(255)->notNull()->defaultValue(''),
                'publisher_email' => $this->string(127)->notNull()->defaultValue(''),
                'publisher_phone' => $this->string(127)->notNull()->defaultValue(''),
                'update_frequency' => $this->string(127)->notNull()->defaultValue(''),
                'import_data_url' => $this->string(255)->notNull()->defaultValue(''),
                'import_schema_url' => $this->string(255)->notNull()->defaultValue(''),
                'hidden' => $this->smallInteger(1)->notNull()->defaultValue(1),
                'created_at' => $this->dateTime()->null()->defaultValue(null),
                'updated_at' => $this->dateTime()->null()->defaultValue(null),
            ],
            $options
        );

        $this->createIndex('hidden', '{{%opendata_passport}}', ['hidden']);

    }

    public function safeDown()
    {
        $this->dropTable('{{%opendata_passport}}');
    }
}
