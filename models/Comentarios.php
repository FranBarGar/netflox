<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comentarios".
 *
 * @property int $id
 * @property string $cuerpo
 * @property int $valoracion
 * @property string $created_at
 * @property string $edited_at
 * @property int $padre_id
 * @property int $show_id
 * @property int $usuario_id
 *
 * @property Comentarios $padre
 * @property Comentarios[] $comentarios
 * @property Shows $show
 * @property Usuarios $usuario
 * @property Votos[] $votos
 * @property Usuarios[] $usuarios
 */
class Comentarios extends \yii\db\ActiveRecord
{
    /**
     * @var array Opciones de ordenacion disponibles.
     */
    const ORDER_BY = [
        'valoracion' => 'Valoracion',
        'votacionTotal' => 'Likes',
        'created_at' => 'Fecha de creacion',
        'edited_at' => 'Fecha de edicion',
    ];

    const SCENARIO_VALORAR = 'valorar';

    const SCENARIO_COMENTAR = 'comentar';

    //Ordenacion

    /** @var string */
    public $orderBy;

    /** @var string */
    public $orderType;

    // Atributos generados por la query de ComentariosSearch.

    /** @var int */
    public $votacionTotal;

    // Likes y Dislikes

    /** @var int */
    public $votoUsuario = null;

    /** @var int */
    public $dislikes = null;

    /** @var int */
    public $likes = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comentarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['show_id', 'usuario_id'], 'required'],
            [['show_id', 'usuario_id', 'padre_id'], 'integer'],
            [['padre_id'], 'required', 'on' => self::SCENARIO_COMENTAR],
            [['padre_id'], 'integer', 'on' => self::SCENARIO_COMENTAR],
            [['valoracion'], 'required', 'on' => self::SCENARIO_VALORAR],
            [['valoracion'], 'number', 'min' => 0.5, 'max' => 5, 'on' => self::SCENARIO_VALORAR],
            [['cuerpo'], 'string'],
            [['padre_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comentarios::className(), 'targetAttribute' => ['padre_id' => 'id'], 'on' => self::SCENARIO_DEFAULT],
            [['show_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['show_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cuerpo' => 'Cuerpo',
            'valoracion' => 'Valoracion',
            'created_at' => 'Created At',
            'padre_id' => 'Padre ID',
            'show_id' => 'Show ID',
            'usuario_id' => 'Usuario ID',
            'orderBy' => 'Ordenar por',
            'orderType' => 'Tipo de ordenacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPadre()
    {
        return $this->hasOne(Comentarios::className(), ['id' => 'padre_id'])->inverseOf('comentarios');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['padre_id' => 'id'])->inverseOf('padre');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id' => 'show_id'])->inverseOf('comentarios');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])->inverseOf('comentarios');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVotos()
    {
        return $this->hasMany(Votos::className(), ['comentario_id' => 'id'])->inverseOf('comentario');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuarios::className(), ['id' => 'usuario_id'])->viaTable('votos', ['comentario_id' => 'id']);
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->setVotoUsuario();
        $this->setVotosLikes();
        $this->setVotosDislikes();
    }

    private function setVotoUsuario()
    {
        if ($this->votoUsuario === null) {
            $votoUsuario = Votos::find()
                ->select('votacion')
                ->andFilterWhere([
                    'usuario_id' => Yii::$app->user->id,
                    'comentario_id' => $this->id
                ])
                ->column();
            $this->votoUsuario = (empty($votoUsuario)) ? 0 : $votoUsuario[0];
        }
    }

    private function setVotosLikes()
    {
        if ($this->likes === null) {
            $likes = Votos::find()
                ->select('SUM(COALESCE(votacion, 0)) AS "likes"')
                ->andWhere([
                    'comentario_id' => $this->id,
                    'votacion' => 1
                ])
                ->groupBy('comentario_id')
                ->column();
            $this->likes = (empty($likes)) ? 0 : $likes[0];
        }
    }

    private function setVotosDislikes()
    {
        if ($this->dislikes === null) {
            $dislikes = Votos::find()
                ->select('COUNT(id) AS "dislikes"')
                ->andWhere([
                    'comentario_id' => $this->id,
                    'votacion' => -1
                ])
                ->groupBy('comentario_id')
                ->column();
            $this->dislikes = (empty($dislikes)) ? 0 : $dislikes[0];
        }
    }
}
