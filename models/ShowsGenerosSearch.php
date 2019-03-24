<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ShowsGeneros;

/**
 * ShowsGenerosSearch represents the model behind the search form of `app\models\ShowsGeneros`.
 */
class ShowsGenerosSearch extends ShowsGeneros
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'show_id', 'genero_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = ShowsGeneros::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'show_id' => $this->show_id,
            'genero_id' => $this->genero_id,
        ]);

        return $dataProvider;
    }
}
