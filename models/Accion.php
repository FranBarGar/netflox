<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accion".
 *
 * @property int $id
 * @property string $accion
 *
 * @property UsuariosShows[] $usuariosShows
 */
class Accion extends \yii\db\ActiveRecord
{
    /** @var string */
    const DROPPED = 'DROPPED';

    /** @var string */
    const WATCHED = 'WATCHED';

    /** @var string */
    const WATCHING = 'WATCHING';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['accion'], 'required'],
            [['accion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'accion' => 'Accion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuariosShows()
    {
        return $this->hasMany(UsuariosShows::className(), ['accion_id' => 'id'])->inverseOf('accion');
    }
}
