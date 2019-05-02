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

    //Ordenacion

    /** @var string */
    public $orderBy;

    /** @var string */
    public $orderType;

    // Atributos generados por la query de ComentariosSearch.

    /** @var int */
    public $votacionTotal;

    /** @var int */
    public $votoUsuario = null;

    /** @var int */
    public $votosTotales = null;

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
            [['valoracion'], 'required', 'on' => self::SCENARIO_VALORAR],
            [['valoracion'], 'number', 'min' => 0.5, 'max' => 5, 'on' => self::SCENARIO_VALORAR],
            [['cuerpo'], 'string'],
            [['padre_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comentarios::className(), 'targetAttribute' => ['padre_id' => 'id']],
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
        $this->setVotosTotales();
    }

    private function setVotoUsuario()
    {
        if ($this->votoUsuario === null) {
            $votoUsuario = Votos::find()
                ->select('votacion')
                ->andFilterWhere([
                    'usuario_id' => Yii::$app->user->id,
                    'comentario_id' => $this->show_id
                ])
                ->column();
            $this->votoUsuario = ($votoUsuario) ?: 0;
        }
    }

    private function setVotosTotales()
    {
        $this->votosTotales = 0;
        if ($this->votoUsuario === null) {
            $votosTotales = Votos::find()
                ->select('SUM(COALESCE(votacion, 0)) AS "votosTotales"')
                ->where(['comentario_id' => $this->show_id])
                ->column();
            $this->votosTotales = ($votosTotales) ?: 0;
        }
    }
}
