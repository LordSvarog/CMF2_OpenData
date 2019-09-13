<?php

use yii\db\Migration;

/**
 * Class m181105_144541_opendata_set_property_addcolumn_map_prop
 */
class m181105_144541_opendata_set_property_addcolumn_map_prop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%opendata_set_property}}', 'map_prop', $this->integer()->null()->after('chart_prop'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%opendata_set_property}}', 'map_prop');
    }
}
