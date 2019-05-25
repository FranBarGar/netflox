<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "archivos".
 *
 * @property int $id
 * @property string $link
 * @property string $descripcion
 * @property int $num_descargas
 * @property int $show_id
 *
 * @property Shows $show
 */
class Archivos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archivos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['show_id'], 'required'],
            [['show_id'], 'integer'],
            [['link', 'descripcion'], 'trim'],
            [['link', 'descripcion'], 'string'],
            [['link'], 'unique'],
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
            'link' => 'Link',
            'descripcion' => 'Descripcion',
            'num_descargas' => 'Num Descargas',
            'show_id' => 'Show ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id' => 'show_id'])->inverseOf('archivos');
    }
}
