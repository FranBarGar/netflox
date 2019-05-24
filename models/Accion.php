<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accion".
 *
 * @property int $id
 * @property string $accion
 */
class Accion extends \yii\db\ActiveRecord
{
    /** @var string */
    const DROPPED = 'DROPPED';

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
}
