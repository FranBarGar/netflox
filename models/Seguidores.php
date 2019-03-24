<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seguidores".
 *
 * @property int $id
 * @property string $created_at
 * @property int $seguidor_id
 * @property int $seguido_id
 *
 * @property Usuarios $seguidor
 * @property Usuarios $seguido
 */
class Seguidores extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seguidores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['seguidor_id', 'seguido_id'], 'required'],
            [['seguidor_id', 'seguido_id'], 'default', 'value' => null],
            [['seguidor_id', 'seguido_id'], 'integer'],
            [['seguidor_id', 'seguido_id'], 'unique', 'targetAttribute' => ['seguidor_id', 'seguido_id']],
            [['seguidor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['seguidor_id' => 'id']],
            [['seguido_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['seguido_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'seguidor_id' => 'Seguidor ID',
            'seguido_id' => 'Seguido ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeguidor()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'seguidor_id'])->inverseOf('seguidores');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeguido()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'seguido_id'])->inverseOf('seguidores0');
    }
}
