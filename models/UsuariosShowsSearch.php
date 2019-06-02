<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UsuariosShows;

/**
 * UsuariosShowsSearch represents the model behind the search form of `app\models\UsuariosShows`.
 */
class UsuariosShowsSearch extends UsuariosShows
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'show_id', 'accion_id'], 'integer'],
            [['created_at', 'ended_at', 'usuario_id'], 'safe'],
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
     * @param $params
     * @param array $ids
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = UsuariosShows::find()
            ->with('accion');

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
            'usuario_id' => $this->usuario_id,
            'show_id' => $this->show_id,
            'accion_id' => $this->accion_id,
            'created_at' => $this->created_at,
            'ended_at' => $this->ended_at,
        ]);

        $query->orderBy('created_at DESC');

        return $dataProvider;
    }
}
