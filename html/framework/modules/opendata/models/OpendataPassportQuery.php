<?php

namespace app\modules\opendata\models;

/**
 * This is the ActiveQuery class for [[OpendataPassport]].
 *
 * @see OpendataPassport
 */
class OpendataPassportQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return OpendataPassport[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OpendataPassport|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
