<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipos".
 *
 * @property int $id
 * @property string $tipo
 * @property string $tipo_duracion
 * @property int $padre_id
 *
 * @property Shows[] $shows
 * @property Tipos $padre
 * @property Tipos[] $tipos
 */
class Tipos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo', 'tipo_duracion'], 'required'],
            [['tipo_duracion'], 'string'],
            [['padre_id'], 'default', 'value' => null],
            [['padre_id'], 'integer'],
            [['tipo'], 'string', 'max' => 255],
            [['tipo'], 'unique'],
            [['padre_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tipos::className(), 'targetAttribute' => ['padre_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'tipo_duracion' => 'Tipo Duracion',
            'padre_id' => 'Padre ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShows()
    {
        return $this->hasMany(Shows::className(), ['tipo_id' => 'id'])->inverseOf('tipo');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPadre()
    {
        return $this->hasOne(Tipos::className(), ['id' => 'padre_id'])->inverseOf('tipos');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipos()
    {
        return $this->hasMany(Tipos::className(), ['padre_id' => 'id'])->inverseOf('padre');
    }
}
