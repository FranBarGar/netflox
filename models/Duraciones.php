<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "duraciones".
 *
 * @property int $id
 * @property string $tipo
 *
 * @property Tipos[] $tipos
 */
class Duraciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'duraciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'required'],
            [['tipo'], 'string', 'max' => 255],
            [['tipo'], 'unique'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipos()
    {
        return $this->hasMany(Tipos::className(), ['duracion_id' => 'id'])->inverseOf('duracion');
    }
}
