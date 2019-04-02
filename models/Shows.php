<?php

namespace app\models;

/**
 * This is the model class for table "shows".
 *
 * @property int $id
 * @property string $titulo
 * @property string $sinopsis
 * @property string $lanzamiento
 * @property int $duracion
 * @property int $imagen_id
 * @property string $trailer
 * @property int $tipo_id
 * @property int $show_id
 *
 * @property Comentarios[] $comentarios
 * @property Participantes[] $participantes
 * @property Archivos $imagen
 * @property Archivos $trailer
 * @property Shows $show
 * @property Shows[] $shows
 * @property Tipos $tipo
 * @property ShowsDescargas[] $showsDescargas
 * @property Archivos[] $archivos
 * @property ShowsGeneros[] $showsGeneros
 * @property Generos[] $generos
 */
class Shows extends \yii\db\ActiveRecord
{
    public $listaGeneros;
    public $imgUpload;
    public $gestor_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo', 'lanzamiento', 'tipo_id'], 'required'],
            [['sinopsis'], 'string'],
            [['lanzamiento'], 'safe'],
            [['duracion', 'imagen_id', 'trailer_id', 'tipo_id', 'show_id'], 'default', 'value' => null],
            [['duracion', 'imagen_id', 'trailer_id', 'tipo_id', 'show_id'], 'integer'],
            [['titulo'], 'string', 'max' => 255],
            [['listaGeneros'], 'each', 'rule' => ['integer']],
            [['imgUpload'], 'image', 'extensions' => 'jpg, gif, png, jpeg'],
            [['trailer_link'], 'url'],
            [['gestor_id'], 'integer'],
            [['imagen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Archivos::className(), 'targetAttribute' => ['imagen_id' => 'id']],
            [['show_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::className(), 'targetAttribute' => ['show_id' => 'id']],
            [['tipo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tipos::className(), 'targetAttribute' => ['tipo_id' => 'id']],
        ];
    }

    public function attributes()
    {
        return parent::attributes();
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'sinopsis' => 'Sinopsis',
            'lanzamiento' => 'Fecha de lanzamiento',
            'duracion' => 'Duracion',
            'imagen_id' => 'Imagen ID',
            'trailer_id' => 'Trailer ID',
            'tipo_id' => 'Tipo de show',
            'show_id' => 'Show al que pertenece',
            'listaGeneros' => 'Generos',
            'imgUpload' => 'Imagen',
            'gestor_id' => 'Gestor de archivos',
            'trailer_link' => 'Enlace del trailer (Youtube, Vimeo...)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantes()
    {
        return $this->hasMany(Participantes::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImagen()
    {
        return $this->hasOne(Archivos::className(), ['id' => 'imagen_id'])->inverseOf('shows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrailer()
    {
        return $this->hasOne(Archivos::className(), ['id' => 'trailer_id'])->inverseOf('shows0');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(self::className(), ['id' => 'show_id'])->inverseOf('shows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShows()
    {
        return $this->hasMany(self::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(Tipos::className(), ['id' => 'tipo_id'])->inverseOf('shows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShowsDescargas()
    {
        return $this->hasMany(ShowsDescargas::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getArchivos()
    {
        return $this->hasMany(Archivos::className(), ['id' => 'archivo_id'])->viaTable('shows_descargas', ['show_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShowsGeneros()
    {
        return $this->hasMany(ShowsGeneros::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getGeneros()
    {
        return $this->hasMany(Generos::className(), ['id' => 'genero_id'])->viaTable('shows_generos', ['show_id' => 'id']);
    }

    /**
     * @return bool
     */
    public function tieneImagen()
    {
        return $this->imagen_id !== null;
    }

    /**
     * @return Shows[]|bool
     */
    public function tieneHijos()
    {
        $shows = $this->shows;
        return empty($shows) ? false : $shows;
    }

    /**
     * @return Generos[]|bool
     */
    public function tieneGeneros()
    {
        $generos = $this->generos;
        if (!empty($generos)) {
            return $generos;
        } elseif ($this->show_id !== null) {
            return $this->show->tieneGeneros();
        }
        return false;
    }

    /**
     * @return Generos[]|bool
     */
    public function getPadreGeneros()
    {
        return $this->show->tieneGeneros();
    }

    /**
     * @param int $id
     * @return \yii\db\ActiveQuery
     */
    public static function findChildrens($id)
    {
        return self::find()
            ->joinWith(['comentarios'])
            ->andWhere(['shows.show_id' => $id])
            ->orderBy('lanzamiento');
    }
}
