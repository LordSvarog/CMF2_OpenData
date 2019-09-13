<?php

use yii\db\Migration;

/**
 * Class m181114_065102_passport_addcolumn_table_view
 */
class m181114_065102_passport_addcolumn_table_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%opendata_passport}}', 'table_view', $this->smallInteger(1)->notNull()->defaultValue(0)->after('chart_type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%opendata_passport}}', 'table_view');
    }
}
