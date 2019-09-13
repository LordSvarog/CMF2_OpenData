<?php

namespace app\modules\opendata\models;

use krok\extend\behaviors\EventBehavior;
use krok\extend\behaviors\TagDependencyBehavior;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "{{%opendata_rating}}".
 *
 * @property integer $id
 * @property integer $passport_id
 * @property integer $count
 * @property double $rating
 *
 * @property OpendataPassport $passport
 */
class OpendataRating extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const MIN_RATE = 1;
    const MAX_RATE = 5;

    /**
     * @var int
     */
    public $rate;

    /**
     * @var int|null
     */
    public $previousRate;

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
            'EventBehavior' => [
                'class' => EventBehavior::class,
                'events' => [
                    BaseActiveRecord::EVENT_BEFORE_VALIDATE => [$this, 'calcRating'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%opendata_rating}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['passport_id', 'count', 'rate', 'previousRate'], 'integer'],
            [['rating'], 'number'],
            [
                ['passport_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => OpendataPassport::className(),
                'targetAttribute' => ['passport_id' => 'id'],
            ],
            [['rate'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
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
            'count' => 'Count',
            'rating' => 'Rating',
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
     * recalculate rating
     *
     * @return void
     */
    public function calcRating()
    {
        if ($this->rate > 0) {
            if ($this->previousRate) {
                $this->rating = round(($this->count * $this->rating - $this->previousRate) / ($this->count - 1), 2);
                $this->count -= 1;

            }
            $this->rating = round(($this->count * $this->rating + $this->rate) / ($this->count + 1), 2);
            $this->count = $this->count + 1;
        }
    }
}
