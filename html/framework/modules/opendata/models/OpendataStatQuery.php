<?php

namespace app\modules\opendata\models;

/**
 * This is the ActiveQuery class for [[OpendataStat]].
 *
 * @see OpendataStat
 */
class OpendataStatQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function roster()
    {
        return $this->andWhere([
            'AND',
            ['IS', OpendataStat::tableName() . '.[[passport_id]]', null],
            ['IS', OpendataStat::tableName() . '.[[set_id]]', null],
        ]);
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function passport($id)
    {
        return $this->andWhere([
            'AND',
            [OpendataStat::tableName() . '.[[passport_id]]' => $id],
            ['IS', OpendataStat::tableName() . '.[[set_id]]', null],
        ]);
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function set($id)
    {
        return $this->andWhere([
            'AND',
            ['IS', OpendataStat::tableName() . '.[[passport_id]]', null],
            [OpendataStat::tableName() . '.[[set_id]]' => $id],
        ]);
    }


    /**
     * @param $format
     *
     * @return $this
     */
    public function format($format)
    {
        return $this->andWhere([
            OpendataStat::tableName() . '.[[format]]' => $format,
        ]);
    }

    /**
     * @inheritdoc
     * @return OpendataStat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OpendataStat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
