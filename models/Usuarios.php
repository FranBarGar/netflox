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
 * @property string $imagen
 * @property string $created_at
 * @property string $rol
 * @property string $token
 * @property string $password
 * @property string $password_repeat
 *
 * @property Comentarios[] $comentarios
 * @property Seguidores[] $seguidores
 * @property Seguidores[] $seguidores0
 * @property Usuarios[] $seguidos
 * @property Usuarios[] $seguidors
 * @property UsuariosShows[] $usuariosShows
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
     * Imagen por defecto.
     * @var string
     */
    const IMAGEN = 'images/user.jpeg';

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
    public function rules()
    {
        return [
            [['nick', 'email', 'password'], 'required'],
            [['nick', 'token'], 'string', 'max' => 32],
            [['nick'], 'unique'],
            [['email', 'biografia'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['imagen'], 'string'],
            [['password_repeat', 'password'], 'string', 'max' => 60],
            [['password_repeat'], 'required', 'on' => self::SCENARIO_CREATE],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'on' => self::SCENARIO_CREATE],
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
            'imagen' => 'Imagen',
            'created_at' => 'Creado el',
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
     * @throws \yii\base\InvalidConfigException
     */
    public function getSeguidos()
    {
        return $this->hasMany(self::className(), ['id' => 'seguido_id'])->viaTable('seguidores', ['seguidor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getSeguidors()
    {
        return $this->hasMany(self::className(), ['id' => 'seguidor_id'])->viaTable('seguidores', ['seguido_id' => 'id']);
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
     * @throws \yii\base\InvalidConfigException
     */
    public function getComentarios0()
    {
        return $this->hasMany(Comentarios::className(), ['id' => 'comentario_id'])->viaTable('votos', ['usuario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuariosShows()
    {
        return $this->hasMany(UsuariosShows::className(), ['usuario_id' => 'id'])->inverseOf('usuario');
    }

    /**
     * Devuelve el enlace a la imagen de portada, en caso de no tener devuelve la imagen por defecto.
     * @return string
     */
    public function getImagenLink()
    {
        return $this->imagen ?: self::IMAGEN;
    }
}
