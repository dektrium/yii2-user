<?php

namespace dektrium\user\models;

use yii\data\ActiveDataProvider;

/**
 * SessionHistorySearch represents the model behind the search form of `dektrium\user\models\SessionHistory`.
 */
class SessionHistorySearch extends SessionHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at'], 'integer'],
            [['user_agent', 'ip'], 'safe'],
        ];
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
        $query = SessionHistory::find()->andWhere([
            'user_id' => $this->user_id,
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['updated_at' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'user_agent', $this->user_agent])
            ->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }
}
