<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gestores_archivos".
 *
 * @property int $id
 * @property string $nombre
 *
 * @property Archivos[] $archivos
 */
class GestoresArchivos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gestores_archivos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'string', 'max' => 255],
            [['nombre'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchivos()
    {
        return $this->hasMany(Archivos::className(), ['gestor_id' => 'id'])->inverseOf('gestor');
    }
}
