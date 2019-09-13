<?php

use yii\db\Migration;

class m170806_070356_import_od_2 extends Migration
{
    public function up()
    {
        if (!YII_ENV_TEST) {
            Yii::$app->runAction('opendata/import-passport');
            Yii::$app->runAction('opendata/import-data');
        }
    }

    public function safeDown()
    {
        $this->truncateTable(\app\modules\opendata\models\OpendataSetValue::tableName());
        $this->truncateTable(\app\modules\opendata\models\OpendataSetProperty::tableName());
        $this->truncateTable(\app\modules\opendata\models\OpendataSet::tableName());
        $this->truncateTable(\app\modules\opendata\models\OpendataPassport::tableName());
    }
}
