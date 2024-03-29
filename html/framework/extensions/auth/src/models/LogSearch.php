<?php

namespace krok\auth\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LogSearch represents the model behind the search form about `krok\auth\models\Log`.
 */
class LogSearch extends Log
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'authId', 'status'], 'integer'],
            [['ip', 'createdAt'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Log::find()->joinWith('authRelation')->orderBy(['createdAt' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            static::tableName() . '.[[id]]' => $this->id,
            static::tableName() . '.[[authId]]' => $this->authId,
            static::tableName() . '.[[status]]' => $this->status,
            static::tableName() . '.[[ip]]' => $this->ip ? ip2long($this->ip) : null,
        ]);

        $query->andFilterWhere(['like', static::tableName() . '.[[createdAt]]', $this->createdAt]);

        return $dataProvider;
    }
}
