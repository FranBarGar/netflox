<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nick
 * @property string $email
 * @property string $biografia
 * @property int $imagen_id
 * @property string $created_at
 * @property string $banned_at
 * @property string $token
 * @property string $password
 *
 * @property Comentarios[] $comentarios
 * @property Seguidores[] $seguidores
 * @property Seguidores[] $seguidores0
 * @property Usuarios[] $seguidos
 * @property Usuarios[] $seguidors
 * @property Archivos $imagen
 * @property Votos[] $votos
 * @property Comentarios[] $comentarios0
 */
class Usuarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nick', 'email', 'imagen_id', 'password'], 'required'],
            [['imagen_id'], 'default', 'value' => null],
            [['imagen_id'], 'integer'],
            [['created_at', 'banned_at'], 'safe'],
            [['nick'], 'string', 'max' => 50],
            [['email', 'biografia'], 'string', 'max' => 255],
            [['token'], 'string', 'max' => 32],
            [['password'], 'string', 'max' => 60],
            [['email'], 'unique'],
            [['nick'], 'unique'],
            [['imagen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Archivos::className(), 'targetAttribute' => ['imagen_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nick' => 'Nick',
            'email' => 'Email',
            'biografia' => 'Biografia',
            'imagen_id' => 'Imagen ID',
            'created_at' => 'Created At',
            'banned_at' => 'Banned At',
            'token' => 'Token',
            'password' => 'Password',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['usuario_id' => 'id'])->inverseOf('usuario');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidores()
    {
        return $this->hasMany(Seguidores::className(), ['seguidor_id' => 'id'])->inverseOf('seguidor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidores0()
    {
        return $this->hasMany(Seguidores::className(), ['seguido_id' => 'id'])->inverseOf('seguido');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidos()
    {
        return $this->hasMany(Usuarios::className(), ['id' => 'seguido_id'])->viaTable('seguidores', ['seguidor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidors()
    {
        return $this->hasMany(Usuarios::className(), ['id' => 'seguidor_id'])->viaTable('seguidores', ['seguido_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImagen()
    {
        return $this->hasOne(Archivos::className(), ['id' => 'imagen_id'])->inverseOf('usuarios');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVotos()
    {
        return $this->hasMany(Votos::className(), ['usuario_id' => 'id'])->inverseOf('usuario');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios0()
    {
        return $this->hasMany(Comentarios::className(), ['id' => 'comentario_id'])->viaTable('votos', ['usuario_id' => 'id']);
    }
}
