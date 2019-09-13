<?php

use yii\db\Migration;

/**
 * Class m171215_132340_opendata_set_record_indexes
 */
class m171215_132340_opendata_set_record_indexes extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropIndex('i-model-record_id', '{{%opendata_set_value}}');
        $this->createIndex('i-model-record_id', '{{%opendata_set_value}}',
            ['model', 'record_id', 'set_id'],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171215_132340_opendata_set_record_indexes cannot be reverted.\n";
    }

}
