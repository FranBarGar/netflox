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
            [['cuerpo'], 'string'],
            [['valoracion', 'padre_id', 'show_id', 'usuario_id'], 'default', 'value' => null],
            [['valoracion', 'padre_id', 'show_id', 'usuario_id'], 'integer'],
            [['created_at'], 'safe'],
            [['show_id', 'usuario_id'], 'required'],
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
}
