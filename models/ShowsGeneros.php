<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shows_generos".
 *
 * @property int $id
 * @property int $show_id
 * @property int $genero_id
 *
 * @property Generos $genero
 * @property Shows $show
 */
class ShowsGeneros extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_generos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['show_id', 'genero_id'], 'required'],
            [['show_id', 'genero_id'], 'default', 'value' => null],
            [['show_id', 'genero_id'], 'integer'],
            [['show_id', 'genero_id'], 'unique', 'targetAttribute' => ['show_id', 'genero_id']],
            [['genero_id'], 'exist', 'skipOnError' => true, 'targetClass' => Generos::className(), 'targetAttribute' => ['genero_id' => 'id']],
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
            'genero_id' => 'Genero ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGenero()
    {
        return $this->hasOne(Generos::className(), ['id' => 'genero_id'])->inverseOf('showsGeneros');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id' => 'show_id'])->inverseOf('showsGeneros');
    }
}
