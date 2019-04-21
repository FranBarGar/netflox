<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "archivos".
 *
 * @property int $id
 * @property string $link
 * @property int $gestor_id
 *
 * @property GestoresArchivos $gestor
 * @property Shows[] $shows
 * @property Shows[] $shows0
 * @property ShowsDescargas[] $showsDescargas
 * @property Shows[] $shows1
 * @property Usuarios[] $usuarios
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
            [['link'], 'string'],
            [['gestor_id'], 'required'],
            [['gestor_id'], 'integer'],
            [['link'], 'unique'],
            [['gestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => GestoresArchivos::className(), 'targetAttribute' => ['gestor_id' => 'id']],
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
            'gestor_id' => 'Gestor ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGestor()
    {
        return $this->hasOne(GestoresArchivos::className(), ['id' => 'gestor_id'])->inverseOf('archivos');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShows()
    {
        return $this->hasMany(Shows::className(), ['imagen_id' => 'id'])->inverseOf('imagen');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShowsDescargas()
    {
        return $this->hasMany(ShowsDescargas::className(), ['archivo_id' => 'id'])->inverseOf('archivo');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getShows1()
    {
        return $this->hasMany(Shows::className(), ['id' => 'show_id'])->viaTable('shows_descargas', ['archivo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuarios::className(), ['imagen_id' => 'id'])->inverseOf('imagen');
    }
}
