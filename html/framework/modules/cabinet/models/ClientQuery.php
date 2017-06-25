<?php

namespace app\modules\cabinet\models;

/**
 * This is the ActiveQuery class for [[Client]].
 *
 * @see Client
 */
class ClientQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Client[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Client|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}