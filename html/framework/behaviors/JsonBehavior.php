<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 27.01.16
 * Time: 18:00
 */

namespace app\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class JsonBehavior
 *
 * ```php
 * public function behaviors()
 * {
 *      return [
 *          'JsonBehavior' => [
 *              'class' => JsonBehavior::className(),
 *              'attribute' => 'options',
 *              'value' => 'options',
 *          ],
 *      ];
 * }
 * ```
 *
 * or
 *
 * ```php
 *
 * public function rules()
 * {
 *      return [
 *          [['options'], 'safe'],
 *      ];
 * }
 *
 * public function behaviors()
 * {
 *      return [
 *          [
 *              'class' => JsonBehavior::className(),
 *              'attribute' => 'common',
 *              'value' => function (array $model) {
 *                  return ArrayHelper::merge(
 *                      $model['options'],
 *                      [
 *                          'title' => [
 *                              'text' => $model['title'],
 *                          ],
 *                      ]
 *                  );
 *              },
 *          ],
 *      ];
 * }
 *
 * ```
 *
 * @package behaviors
 */
class JsonBehavior extends AttributeBehavior
{
    /**
     * @var string
     */
    public $attribute = 'options';

    /**
     * @var array
     */
    public $events = [];

    /**
     * @return array
     */
    public function events()
    {
        if (empty($this->events)) {
            return [
                ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
                ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ];
        } else {
            return $this->events;
        }
    }

    /**
     * @param \yii\base\Event $event
     */
    public function afterFind($event)
    {
        $event->sender->{$this->attribute} = Json::decode($event->sender->{$this->attribute});
    }

    /**
     * @param \yii\base\Event $event
     */
    public function beforeInsert($event)
    {
        $event->sender->{$this->attribute} = Json::encode(
            ArrayHelper::getValue($event->sender->toArray(), $this->value, [])
        );
    }
}
