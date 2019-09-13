<?php

namespace app\modules\opendata\models;

/**
 * This is the model class for table "{{%opendata_stat}}".
 *
 * @property integer $id
 * @property integer $passport_id
 * @property integer $set_id
 * @property string $format
 * @property integer $count
 * @property integer $size
 *
 * @property OpendataPassport $passport
 * @property OpendataSet $set
 */
class OpendataStat extends \yii\db\ActiveRecord
{

    const FORMAT_SHOWS = 'html';
    const FORMAT_XML = 'xml';
    const FORMAT_JSON = 'json';
    const FORMAT_CSV = 'json';

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
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%opendata_stat}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['passport_id', 'set_id', 'count', 'size'], 'integer'],
            [['format'], 'string', 'max' => 15],
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
            'format' => 'Format',
            'count' => 'Count',
            'size' => 'Size',
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
     * @inheritdoc
     * @return OpendataStatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OpendataStatQuery(get_called_class());
    }
}
