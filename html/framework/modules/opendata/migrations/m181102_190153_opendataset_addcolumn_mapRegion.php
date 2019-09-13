<?php

use yii\db\Migration;

/**
 * Class m181102_190153_opendataset_addcolumn_mapRegion
 */
class m181102_190153_opendataset_addcolumn_mapRegion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%opendata_set}}', 'map_region', $this->smallInteger(1)->notNull()->defaultValue(0)->after('changes'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%opendata_set}}','map_region');
    }
}
