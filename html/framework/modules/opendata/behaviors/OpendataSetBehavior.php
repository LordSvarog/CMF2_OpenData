<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 13.11.17
 * Time: 12:38
 */

namespace app\modules\opendata\behaviors;

use app\modules\opendata\models\OpendataPassport;
use app\modules\opendata\models\OpendataSet;
use app\modules\opendata\models\OpendataSetProperty;
use app\modules\opendata\models\OpendataSetValue;
use krok\grid\interfaces\HiddenAttributeInterface;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class OpendataSetBehavior
 *
 * @package app\modules\opendata\behaviors
 */
class OpendataSetBehavior extends Behavior
{
    /**
     * @var string
     */
    public $passportCode;

    /**
     * @var array
     */
    public $attributeHandlers = [];

    /**
     * @var array
     */
    public $attributes2property = ['title' => 'name'];

    /**
     * @var integer|null
     */
    public $itemsLimit;

    /**
     * @var string
     */
    public $hiddenAttribute = 'hidden';

    /**
     * @var array
     */
    public $scenarios = [
        ActiveRecord::SCENARIO_DEFAULT,
    ];

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->passportCode) {
            throw new InvalidConfigException('Property "passportCode" must be set.');
        }
        parent::init();
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @return bool
     */
    public function afterInsert()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        if (in_array($owner->getScenario(), $this->scenarios)) {
            $set = OpendataSet::find()
                ->joinWith('passport')
                ->where([
                    OpendataPassport::tableName() . '.[[code]]' => $this->passportCode,
                ])->orderBy([
                    OpendataSet::tableName() . '.[[created_at]]' => SORT_DESC,
                ])->limit(1)->one();


            if (!$set) {
                return false;
            }


            $properties = ArrayHelper::getColumn($set->properties, 'name');

            $value = [];

            // fill value of set
            foreach ($this->attributes2property as $attribute => $property) {
                $value[$property] = array_key_exists($attribute, $this->attributeHandlers) ?
                    call_user_func($this->attributeHandlers[$attribute], $owner) :
                    $owner->getAttribute($attribute);

                // create new property if not exists
                if (!in_array($property, $properties)) {
                    (new OpendataSetProperty([
                        'passport_id' => $set->passport_id,
                        'set_id' => $set->id,
                        'name' => $property,
                        'title' => $owner->getAttributeLabel($attribute),
                        'type' => OpendataSetProperty::TYPE_STRING,
                    ]))->save();
                    array_push($properties, $property);
                }
            }

            $valueModel = OpendataSetValue::findOne([
                'model' => get_class($owner),
                'record_id' => $owner->id,
                'set_id' => $set->id,
            ]);

            $hidden = false;
            if (is_string($this->hiddenAttribute)) {
                $hidden = $owner->getAttribute($this->hiddenAttribute) === HiddenAttributeInterface::HIDDEN_YES;
            } elseif (is_callable($this->hiddenAttribute)) {
                $hidden = call_user_func($this->hiddenAttribute, $owner);
            }

            if ($hidden) {
                if ($valueModel) {
                    $valueModel->delete();
                }

                return false;
            }
            if (!$valueModel) {
                $valueModel = new OpendataSetValue([
                    'model' => get_class($owner),
                    'record_id' => $owner->id,
                    'set_id' => $set->id,
                ]);
                $set->updated_at = new Expression('NOW()');
                $set->save();
            }

            $valueModel->value = $value;

            $saved = $valueModel->save();

            if ($this->itemsLimit) {
                $list = OpendataSetValue::find()->where([
                    'model' => get_class($owner),
                ])->orderBy(['id' => SORT_DESC])
                    ->offset($this->itemsLimit)
                    ->all();

                foreach ($list as $model) {
                    $model->delete();
                }
            }

            return $saved;
        }

        return false;
    }

    /**
     * @return void
     */
    public function afterDelete()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        OpendataSetValue::deleteAll([
            'model' => get_class($owner),
            'record_id' => $owner->id,
        ]);
    }
}
