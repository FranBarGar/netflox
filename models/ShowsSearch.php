<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ShowsSearch represents the model behind the search form of `app\models\Shows`.
 */
class ShowsSearch extends Shows
{
    //Filtrado
    /** @var array */
    public $listaGeneros;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo_id'], 'integer'],
            [['titulo', 'sinopsis', 'lanzamiento', 'listaGeneros', 'orderBy', 'orderType'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), [$this->listaGeneros]);
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
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Shows::find();

        // add conditions that should always apply here
        $query->select('
                shows.*, 
                SUM(COALESCE(valoracion, 0))/GREATEST(COUNT(valoracion), 1)::float AS "valoracionMedia",
                count(comentarios.id) AS "numComentarios"
            ')
            ->leftJoin('tipos', 'shows.tipo_id = tipos.id')
            ->joinWith('tipos')
            ->joinWith('showsGeneros')
            ->joinWith('comentarios')
            ->with('generos')
//            ->with('imagen')
            ->where(['tipos.padre_id' => null])
            ->andFilterHaving(['not', ['valoracion' => null]])
            ->groupBy('shows.id');

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
            'lanzamiento' => $this->lanzamiento,
            'tipo_id' => $this->tipo_id,
            'genero_id' => $this->listaGeneros,
        ]);

        $query->andFilterWhere(['ilike', 'titulo', $this->titulo])
            ->andFilterWhere(['ilike', 'sinopsis', $this->sinopsis]);

        if ($this->listaGeneros != '') {
            $query->filterHaving(['>=', 'count(*)', count($this->listaGeneros)]);
        }

        if ($this->orderBy != null) {
            $query->orderBy($this->orderBy . ' ' . $this->orderType);
        }

        return $dataProvider;
    }
}
