<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tipos;

/**
 * TiposSearch represents the model behind the search form of `app\models\Tipos`.
 */
class TiposSearch extends Tipos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'padre_id'], 'integer'],
            [['tipo', 'tipo_duracion'], 'safe'],
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
        $query = Tipos::find()
            ->joinWith('tipos AS padre');

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
            'padre_id' => $this->padre_id,
        ]);

        $query->andFilterWhere(['ilike', 'tipos.tipo', $this->tipo])
            ->andFilterWhere(['ilike', 'tipos.tipo_duracion', $this->tipo_duracion]);

        return $dataProvider;
    }
}
