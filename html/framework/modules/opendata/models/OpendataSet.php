<?php

namespace app\modules\opendata\models;

use krok\extend\behaviors\TagDependencyBehavior;
use krok\grid\interfaces\HiddenAttributeInterface;
use krok\grid\traits\HiddenAttributeTrait;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%opendata_set}}".
 *
 * @property integer $id
 * @property integer $passport_id
 * @property string $title
 * @property string $version
 * @property string $changes
 * @property integer $map_region
 * @property integer $hidden
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OpendataPassport $passport
 * @property OpendataSetProperty[] $properties
 * @property OpendataSetValue[] $propertyValues
 * @property OpendataStat[] $stat
 */
class OpendataSet extends \yii\db\ActiveRecord implements HiddenAttributeInterface
{
    use HiddenAttributeTrait;

    const YESNO_NO = 0;
    const YESNO_YES = 1;

    const PER_PAGE_10 = 10;
    const PER_PAGE_20 = 20;
    const PER_PAGE_50 = 50;
    const PER_PAGE_100 = 100;

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
            //'TimestampBehavior' => TimestampBehavior::class,
            'TagDependencyBehavior' => TagDependencyBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%opendata_set}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['passport_id', 'hidden', 'map_region'], 'integer'],
            [['title', 'version', 'changes'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 512],
            [['version', 'changes'], 'string', 'max' => 127],
            [
                ['passport_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => OpendataPassport::className(),
                'targetAttribute' => ['passport_id' => 'id'],
            ],
            [['map_region'], 'default', 'value' => 0],
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
            'title' => 'Заголовок',
            'version' => 'Версия',
            'changes' => 'Изменения',
            'map_region' => 'Карта регионов',
            'hidden' => 'Скрыт',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
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
    public function getProperties()
    {
        return $this->hasMany(OpendataSetProperty::className(), ['set_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPropertyValues()
    {
        return $this->hasMany(OpendataSetValue::className(), ['set_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStat()
    {
        return $this->hasMany(OpendataStat::className(), ['set_id' => 'id']);
    }


    /**
     * @inheritdoc
     * @return OpendataSetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OpendataSetQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getVersionDate()
    {
        return Yii::$app->formatter->asDate($this->updated_at,
            'php:Ymd');
    }

    /**
     * @return array
     */
    public static function getDelimiterList()
    {
        return [
            ',' => 'Запятая (,)',
            ';' => 'Точка с запятой (;)',
        ];
    }

    /**
     *
     * @return boolean
     */
    public function hasChart()
    {
        if ($this->passport->chart_type) {
            $chartProp = ArrayHelper::getColumn($this->properties, 'chart_prop');

            $series = array_search(OpendataSetProperty::CHART_PROP_SERIES, $chartProp);
            $cat = array_search(OpendataSetProperty::CHART_PROP_CATEGORY, $chartProp);

            return ($series !== false && $cat !== false);
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    public function hasMap()
    {
        if ($this->map_region) {
            $chartProp = ArrayHelper::getColumn($this->properties, 'map_prop');

            $region = array_search(OpendataSetProperty::MAP_PROP_REGION, $chartProp);
            $indicator = array_search(OpendataSetProperty::MAP_PROP_INDICATOR, $chartProp);

            return ($region !== false && $indicator !== false);
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    public function hasTable()
    {
        return $this->passport->table_view;
    }

    /**
     * @return array
     */
    public static function getYesNoList()
    {
        return [
            self::YESNO_NO => 'Нет',
            self::YESNO_YES => 'Да',
        ];
    }

    /**
     * @return array
     */
    public static function getPerPageList()
    {
        return [
            self::PER_PAGE_10,
            self::PER_PAGE_20,
            self::PER_PAGE_50,
            self::PER_PAGE_100,
        ];
    }

    /**
     * @return string
     */
    public function getMapRegion()
    {
        return ArrayHelper::getValue(static::getYesNoList(), $this->hidden);
    }
}
