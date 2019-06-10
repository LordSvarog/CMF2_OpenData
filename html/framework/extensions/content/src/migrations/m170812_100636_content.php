<?php

use krok\grid\interfaces\HiddenAttributeInterface;
use yii\db\Migration;

class m170812_100636_content extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%content}}', [
            'alias' => 'index',
            'title' => 'Главная',
            'text' => 'Главная страница',
            'layout' => '//index',
            'view' => 'index',
            'hidden' => HiddenAttributeInterface::HIDDEN_NO,
            'language' => Yii::$app->language,
            'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
            'updatedAt' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);

        $this->insert('{{%content}}', [
            'alias' => 'about',
            'title' => 'О нас',
            'text' => 'Страница о нас',
            'layout' => '//common',
            'view' => 'about',
            'hidden' => HiddenAttributeInterface::HIDDEN_NO,
            'language' => Yii::$app->language,
            'createdAt' => (new DateTime())->format('Y-m-d H:i:s'),
            'updatedAt' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%content}}', ['alias' => ['index', 'about']]);
    }
}
