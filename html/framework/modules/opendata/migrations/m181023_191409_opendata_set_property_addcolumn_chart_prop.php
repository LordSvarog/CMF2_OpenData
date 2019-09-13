<?php

use yii\db\Migration;

/**
 * Class m181023_191409_opendata_set_property_addcolumn_chart_prop
 */
class m181023_191409_opendata_set_property_addcolumn_chart_prop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%opendata_set_property}}', 'chart_prop', $this->integer()->null()->after('type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%opendata_set_property}}', 'chart_prop');
    }
}
