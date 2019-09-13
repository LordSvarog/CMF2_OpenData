<?php

namespace app\modules\opendata\models;

use krok\extend\behaviors\TagDependencyBehavior;
use krok\extend\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%opendata_set_property}}".
 *
 * @property integer $id
 * @property integer $passport_id
 * @property integer $set_id
 * @property string $name
 * @property string $title
 * @property string $type
 * @property integer $chart_prop
 * @property integer $map_prop
 * @property string $created_at
 * @property string $updated_at
 *
 * @property int $delete
 *
 * @property OpendataPassport $passport
 * @property OpendataSet $set
 * @property OpendataSetValue[] $opendataSetPropertyValues
 */
class OpendataSetProperty extends \yii\db\ActiveRecord
{
    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'decimal';

    const CHART_PROP_SERIES = 1;
    const CHART_PROP_CATEGORY = 2;

    const MAP_PROP_REGION = 1;
    const MAP_PROP_INDICATOR = 2;

    /**
     * @var int
     */
    public $delete = 0;

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
            'TimestampBehavior' => TimestampBehavior::class,
            'TagDependencyBehavior' => TagDependencyBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%opendata_set_property}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['passport_id', 'set_id', 'delete', 'chart_prop', 'map_prop'], 'integer'],
            [['name', 'title', 'passport_id', 'set_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'type'], 'string', 'max' => 127],
            [['title'], 'string', 'max' => 512],
            [
                ['passport_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => OpendataPassport::className(),
                'targetAttribute' => ['passport_id' => 'id'],
            ],
            [
                ['set_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => OpendataSet::className(),
                'targetAttribute' => ['set_id' => 'id'],
            ],
            [
                ['name'],
                'filter',
                'filter' => function ($value) {
                    return preg_replace('#([^a-z\_\d]+)#i', '', $value);
                },
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
            'passport_id' => 'Passport ID',
            'set_id' => 'Set ID',
            'name' => 'Код свойства',
            'title' => 'Название',
            'type' => 'Тип',
            'chart_prop' => 'На диаграмме',
            'map_prop' => 'На карте',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPassport()
    {
        return $this->hasOne(OpendataPassport::className(), ['id' => 'passport_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSet()
    {
        return $this->hasOne(OpendataSet::className(), ['id' => 'set_id']);
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_STRING => 'Строка',
            self::TYPE_NUMBER => 'Число',
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return ArrayHelper::getValue(self::getTypeList(), $this->type);
    }

    /**
     * @return array
     */
    public static function getChartPropList()
    {
        return [
            self::CHART_PROP_SERIES => 'Ряды',
            self::CHART_PROP_CATEGORY => 'Категория',
        ];
    }

    /**
     * @return string
     */
    public function getChartPropAsString()
    {
        return ArrayHelper::getValue(self::getChartPropList(), $this->chart_prop);
    }

    /**
     * @return array
     */
    public static function getMapPropList()
    {
        return [
            self::MAP_PROP_REGION => 'Регион',
            self::MAP_PROP_INDICATOR => 'Показатель',
        ];
    }

    /**
     * @return string
     */
    public function getMapPropAsString()
    {
        return ArrayHelper::getValue(self::getMapPropList(), $this->map_prop);
    }

    /**
     * @inheritdoc
     * @return OpendataSetPropertyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OpendataSetPropertyQuery(get_called_class());
    }
}
