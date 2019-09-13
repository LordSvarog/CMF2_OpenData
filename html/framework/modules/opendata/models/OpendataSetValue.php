<?php

namespace app\modules\opendata\models;

use krok\extend\behaviors\JsonBehavior;
use krok\extend\behaviors\TagDependencyBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%opendata_set_value}}".
 *
 * @property integer $id
 * @property integer $set_id
 * @property array $value
 * @property string $model
 * @property integer $record_id
 *
 * @property OpendataSetProperty $property
 * @property OpendataSet $set
 */
class OpendataSetValue extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'TagDependencyBehavior' => TagDependencyBehavior::class,
            'JsonBehavior' => [
                'class' => JsonBehavior::class,
                'attribute' => 'value',
                'value' => 'value',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%opendata_set_value}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['set_id'], 'integer'],
            [['value', 'model', 'record_id'], 'safe'],
            [
                ['set_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => OpendataSet::className(),
                'targetAttribute' => ['set_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'set_id' => 'Set ID',
            'value' => 'Value',
            'model' => 'Model className',
            'record_id' => 'Record id',
        ];
    }

    /**
     * @param $name
     *
     * @return string|null
     */
    public function getPropertyValue($name)
    {
        return ArrayHelper::getValue($this->value, $name);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSet()
    {
        return $this->hasOne(OpendataSet::className(), ['id' => 'set_id']);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!in_array($name, $this->attributes())) {
            return ArrayHelper::getValue($this->value, $name, '');
        }

        return parent::__get($name);
    }
}
