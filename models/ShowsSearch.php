<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ShowsSearch represents the model behind the search form of `app\models\Shows`.
 */
class ShowsSearch extends Shows
{
    //Filtrado
    /**
     * @var array Generos que deben tener los show.
     */
    public $listaGeneros;

    /**
     * @var int Id de la accion a buscar.
     */
    public $accion;

    //Ordenacion
    /**
     * @var string Parametro para la ordenacion de shows.
     */
    public $orderBy;

    /**
     * @var string Parametro para la ordenacion de shows.
     */
    public $orderType;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo_id'], 'integer'],
            [['titulo', 'sinopsis', 'lanzamiento', 'listaGeneros', 'orderBy', 'orderType', 'accion'], 'safe'],
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
            ->joinWith('tipo')
            ->joinWith('showsGeneros')
            ->joinWith('comentarios')
            ->joinWith('usuariosShows')
            ->with('generos')
            ->where([
                'tipos.padre_id' => null,
                'ended_at' => null,
            ])
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
            'tipo_id' => $this->tipo_id,
            'genero_id' => $this->listaGeneros,
        ]);

        $query->andFilterWhere(['ilike', 'titulo', $this->titulo]);

        if ($this->listaGeneros != '') {
            $query->filterHaving(['>=', 'count(*)', count($this->listaGeneros)]);
        }

        if ($this->orderBy != '') {
            $query->orderBy($this->orderBy . ' ' . $this->orderType);
        } else {
            $query->orderBy('valoracionMedia DESC');
        }

        if ($this->accion != '') {
            $query->andFilterWhere([
                'usuarios_shows.usuario_id' => Yii::$app->user->id,
                'accion_id' => $this->accion,
            ]);
        } else {
            $dropped = UsuariosShows::find()
                ->select('show_id')
                ->andWhere([
                    'accion_id' => Accion::findOne(['accion' => Accion::DROPPED])->id,
                    'usuarios_shows.usuario_id' => Yii::$app->user->id
                ])
                ->column();
            $query->andWhere([
                'not', [
                    'shows.id' => $dropped
                ]
            ]);
        }

        return $dataProvider;
    }
}
