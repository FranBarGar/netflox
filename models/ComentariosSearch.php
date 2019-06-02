<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Comentarios;

/**
 * ComentariosSearch represents the model behind the search form of `app\models\Comentarios`.
 */
class ComentariosSearch extends Comentarios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'padre_id', 'show_id'], 'integer'],
            [['valoracion'], 'number'],
            [['cuerpo', 'created_at', 'orderBy', 'orderType', 'usuario_id'], 'safe'],
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
//        var_dump($_POST, $_GET);die;
        $query = Comentarios::find()
            ->select('
                comentarios.*,
                SUM(COALESCE(votacion, 0)) AS "votacionTotal"
            ');

        // add conditions that should always apply here
        $query
            ->andWhere(['not', ['valoracion' => null]])
            ->joinWith('votos')
            ->groupBy('comentarios.id');

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
            'valoracion' => $this->valoracion,
            'created_at' => $this->created_at,
            'padre_id' => $this->padre_id,
            'show_id' => $this->show_id,
            'comentarios.usuario_id' => $this->usuario_id,
        ]);

        $query->andFilterWhere(['ilike', 'cuerpo', $this->cuerpo]);

        if ($this->orderBy != null) {
            $query->orderBy($this->orderBy . ' ' . $this->orderType . ', created_at');
        } else {
            $query->orderBy('created_at DESC');
        }

        return $dataProvider;
    }
}
