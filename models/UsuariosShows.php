<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios_shows".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $show_id
 * @property string $action
 * @property string $created_at
 * @property string $ended_at
 *
 * @property Shows $show
 * @property Usuarios $usuario
 */
class UsuariosShows extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios_shows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'show_id', 'action'], 'required'],
            [['usuario_id', 'show_id'], 'default', 'value' => null],
            [['usuario_id', 'show_id'], 'integer'],
            [['created_at', 'ended_at'], 'safe'],
            [['action'], 'string', 'max' => 255],
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
            'usuario_id' => 'Usuario ID',
            'show_id' => 'Show ID',
            'action' => 'Action',
            'created_at' => 'Created At',
            'ended_at' => 'Ended At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id' => 'show_id'])->inverseOf('usuariosShows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])->inverseOf('usuariosShows');
    }
}
