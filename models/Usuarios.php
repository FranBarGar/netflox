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
class Usuarios extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * Escenario de registro de usuarios.
     * @var string
     */
    const SCENARIO_CREATE = 'create';

    /**
     * Confirmar contraseña.
     * @var string
     */
    public $password_repeat;

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
            [['nick', 'email', 'password'], 'required'],
            [['nick', 'email'], 'unique'],
            [['email', 'biografia'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['token', 'nick'], 'string', 'max' => 32],
            [['password', 'password_repeat'], 'string', 'max' => 60],
            [['password_repeat'], 'required', 'on' => self::SCENARIO_CREATE],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'on' => self::SCENARIO_CREATE],
            [['imagen_id'], 'default', 'value' => 1],
            [['imagen_id'], 'integer'],
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
            'nick' => 'Nombre de usuario',
            'email' => 'Email',
            'biografia' => 'Biografia',
            'imagen_id' => 'Imagen',
            'created_at' => 'Creado el',
            'banned_at' => 'Baneado el',
            'token' => 'Token',
            'password' => 'Contraseña',
            'password_repeat' => 'Confirmar contraseña',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), ['password_repeat']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    /**
     * Finds user by username.
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['nick' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
    }

    /**
     * Validates password.
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert && $this->scenario === self::SCENARIO_CREATE) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
            $this->token = Yii::$app->security->generateRandomString();
        }
        return true;
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
        return $this->hasMany(self::className(), ['id' => 'seguido_id'])->viaTable('seguidores', ['seguidor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidors()
    {
        return $this->hasMany(self::className(), ['id' => 'seguidor_id'])->viaTable('seguidores', ['seguido_id' => 'id']);
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
