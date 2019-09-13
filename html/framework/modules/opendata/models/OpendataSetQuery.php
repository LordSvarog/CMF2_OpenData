<?php

namespace app\modules\opendata\models;

/**
 * This is the ActiveQuery class for [[OpendataSet]].
 *
 * @see OpendataSet
 */
class OpendataSetQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return OpendataSet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OpendataSet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
