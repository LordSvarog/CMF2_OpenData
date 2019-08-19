<?php

namespace krok\auth\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AuthSearch represents the model behind the search form about `krok\auth\models\Auth`.
 */
class AuthSearch extends Auth
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'blocked'], 'integer'],
            [['login', 'email', 'createdAt', 'updatedAt'], 'safe'],
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
        $query = Auth::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'blocked' => $this->blocked,
        ]);

        $query
            ->andFilterWhere(['like', 'login', $this->login])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'createdAt', $this->createdAt])
            ->andFilterWhere(['like', 'updatedAt', $this->updatedAt]);

        return $dataProvider;
    }
}
