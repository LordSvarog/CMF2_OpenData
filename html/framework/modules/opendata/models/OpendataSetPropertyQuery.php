<?php

namespace app\modules\opendata\models;

/**
 * This is the ActiveQuery class for [[OpendataSetProperty]].
 *
 * @see OpendataSetProperty
 */
class OpendataSetPropertyQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return OpendataSetProperty[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OpendataSetProperty|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function set($id)
    {
        return $this->andWhere([
            OpendataSetProperty::tableName() . '.[[set_id]]' => $id,
        ]);
    }

    /**
     * @param $chartProp
     *
     * @return $this
     */
    public function chart($chartProp)
    {
        return $this->andWhere([
            OpendataSetProperty::tableName() . '.[[chart_prop]]' => $chartProp,
        ]);
    }

    /**
     * @param $mapProp
     *
     * @return $this
     */
    public function map($mapProp)
    {
        return $this->andWhere([
            OpendataSetProperty::tableName() . '.[[map_prop]]' => $mapProp,
        ]);
    }
}
