<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shows_descargas".
 *
 * @property int $id
 * @property int $num_descargas
 * @property int $archivo_id
 * @property int $show_id
 *
 * @property Archivos $archivo
 * @property Shows $show
 */
class ShowsDescargas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_descargas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['num_descargas', 'archivo_id', 'show_id'], 'default', 'value' => null],
            [['num_descargas', 'archivo_id', 'show_id'], 'integer'],
            [['archivo_id', 'show_id'], 'required'],
            [['show_id', 'archivo_id'], 'unique', 'targetAttribute' => ['show_id', 'archivo_id']],
            [['archivo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Archivos::className(), 'targetAttribute' => ['archivo_id' => 'id']],
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
            'num_descargas' => 'Num Descargas',
            'archivo_id' => 'Archivo ID',
            'show_id' => 'Show ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchivo()
    {
        return $this->hasOne(Archivos::className(), ['id' => 'archivo_id'])->inverseOf('showsDescargas');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id' => 'show_id'])->inverseOf('showsDescargas');
    }
}
