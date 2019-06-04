<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seguidores".
 *
 * @property int $id
 * @property string $created_at
 * @property string $ended_at
 * @property string $blocked_at
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
     * Devuelve un modelo o null en caso de no ser ni seguidor ni bloqueador.
     * @param $seguido_id
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function soySeguidorOBloqueador($seguido_id)
    {
        return Seguidores::find()
                ->andFilterWhere([
                    'seguido_id' => $seguido_id,
                    'seguidor_id' => Yii::$app->user->id,
                ])
                ->andWhere([
                    'ended_at' => null,
                ])
                ->one();
    }

    /**
     * Devuelve la lista de id de las personas a las que sigue un usuario o con un 0 en caso de no tener seguidores.
     *
     * @param $id
     * @return array
     */
    public static function getSeguidoresId($id)
    {
        return Seguidores::find()
            ->select('seguido_id')
            ->andWhere([
                'seguidor_id' => $id,
                'ended_at' => null,
                'blocked_at' => null
            ])
            ->column() ?: [0];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['seguidor_id', 'seguido_id'], 'required'],
            [['seguidor_id', 'seguido_id'], 'integer'],
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
