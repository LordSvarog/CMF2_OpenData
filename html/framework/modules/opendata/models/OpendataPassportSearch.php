<?php

namespace app\modules\opendata\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OpendataPassportSearch represents the model behind the search form about `app\modules\opendata\models\OpendataPassport`.
 */
class OpendataPassportSearch extends OpendataPassport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hidden'], 'integer'],
            [
                [
                    'title',
                    'code',
                    'description',
                    'subject',
                    'owner',
                    'publisher_name',
                    'publisher_email',
                    'publisher_phone',
                    'update_frequency',
                    'import_data_url',
                    'import_schema_url',
                    'created_at',
                    'updated_at',
                ],
                'safe',
            ],
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
        $query = OpendataPassport::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'hidden' => $this->hidden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'owner', $this->owner])
            ->andFilterWhere(['like', 'publisher_name', $this->publisher_name])
            ->andFilterWhere(['like', 'publisher_email', $this->publisher_email])
            ->andFilterWhere(['like', 'publisher_phone', $this->publisher_phone])
            ->andFilterWhere(['like', 'update_frequency', $this->update_frequency])
            ->andFilterWhere(['like', 'import_data_url', $this->import_data_url])
            ->andFilterWhere(['like', 'import_schema_url', $this->import_schema_url]);

        return $dataProvider;
    }
}
