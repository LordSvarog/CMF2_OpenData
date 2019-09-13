<?php

use yii\db\Migration;

/**
 * Class m181023_205143_opendata_password_addcolumn_chart_type
 */
class m181023_205143_opendata_password_addcolumn_chart_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%opendata_passport}}', 'chart_type', $this->integer()->null()->after('import_schema_url'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%opendata_passport}}', 'chart_type');
    }
}
