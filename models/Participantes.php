<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "participantes".
 *
 * @property int $id
 * @property int $show_id
 * @property int $persona_id
 * @property int $rol_id
 *
 * @property Personas $persona
 * @property Roles $rol
 * @property Shows $show
 */
class Participantes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'participantes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['show_id', 'persona_id', 'rol_id'], 'required'],
            [['show_id', 'persona_id', 'rol_id'], 'default', 'value' => null],
            [['show_id', 'persona_id', 'rol_id'], 'integer'],
            [['show_id', 'persona_id', 'rol_id'], 'unique', 'targetAttribute' => ['show_id', 'persona_id', 'rol_id']],
            [['persona_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personas::className(), 'targetAttribute' => ['persona_id' => 'id']],
            [['rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['rol_id' => 'id']],
            [['show_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['show_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'show_id' => 'Show ID',
            'persona_id' => 'Persona ID',
            'rol_id' => 'Rol ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersona()
    {
        return $this->hasOne(Personas::className(), ['id' => 'persona_id'])->inverseOf('participantes');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRol()
    {
        return $this->hasOne(Roles::className(), ['id' => 'rol_id'])->inverseOf('participantes');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id' => 'show_id'])->inverseOf('participantes');
    }
}
