<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios_shows".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $show_id
 * @property int $accion_id
 * @property string $created_at
 * @property string $ended_at
 *
 * @property Shows $show
 * @property Shows $accion
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
            [['usuario_id', 'show_id'], 'required'],
            [['usuario_id', 'show_id', 'accion_id'], 'default', 'value' => null],
            [['usuario_id', 'show_id', 'accion_id'], 'integer'],
            [['created_at', 'ended_at'], 'safe'],
            [['show_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['show_id' => 'id']],
            [['accion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['accion_id' => 'id']],
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
            'accion_id' => 'Accion ID',
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
    public function getAccion()
    {
        return $this->hasOne(Shows::className(), ['id' => 'accion_id'])->inverseOf('usuariosShows0');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])->inverseOf('usuariosShows');
    }

    /**
     * Busca una instancia del modelo, en caso de no encontrarla devuelve una ya parametrizada.
     * @param $id
     * @return UsuariosShows|array|\yii\db\ActiveRecord|null
     */
    public static function findOrEmpty($id)
    {
        $accion = UsuariosShows::find()
            ->andWhere([
                'usuario_id' => Yii::$app->user->id,
                'show_id' => $id,
                'ended_at' => null
            ])
            ->one();

        if ($accion == null) {
            $accion = new UsuariosShows();
            $accion->show_id = $id;
            $accion->usuario_id = Yii::$app->user->id;
        }

        return $accion;
    }
}
