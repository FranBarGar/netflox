<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "votos".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $comentario_id
 * @property int $votacion
 *
 * @property Comentarios $comentario
 * @property Usuarios $usuario
 */
class Votos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'votos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'comentario_id'], 'required'],
            [['usuario_id', 'comentario_id', 'votacion'], 'default', 'value' => null],
            [['usuario_id', 'comentario_id', 'votacion'], 'integer'],
            [['usuario_id', 'comentario_id'], 'unique', 'targetAttribute' => ['usuario_id', 'comentario_id']],
            [['comentario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comentarios::className(), 'targetAttribute' => ['comentario_id' => 'id']],
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
            'usuario_id' => 'Usuario ID',
            'comentario_id' => 'Comentario ID',
            'votacion' => 'Votacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComentario()
    {
        return $this->hasOne(Comentarios::className(), ['id' => 'comentario_id'])->inverseOf('votos');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])->inverseOf('votos');
    }
}
