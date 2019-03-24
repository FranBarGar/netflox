<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "generos".
 *
 * @property int $id
 * @property string $genero
 *
 * @property ShowsGeneros[] $showsGeneros
 * @property Shows[] $shows
 */
class Generos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'generos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['genero'], 'required'],
            [['genero'], 'string', 'max' => 255],
            [['genero'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'genero' => 'Genero',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShowsGeneros()
    {
        return $this->hasMany(ShowsGeneros::className(), ['genero_id' => 'id'])->inverseOf('genero');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShows()
    {
        return $this->hasMany(Shows::className(), ['id' => 'show_id'])->viaTable('shows_generos', ['genero_id' => 'id']);
    }
}
